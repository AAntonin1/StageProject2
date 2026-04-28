<script setup>
import {inject, onMounted, onUnmounted, ref, watch} from 'vue';

const props = defineProps({
  placeholder: String,
  workAddress: Object,
  labelName: String
});

const address = defineModel('address');
const query = ref('');
const results = ref([]);
const showResults = ref(false);
let debounceTimeout = null;
const isSelecting = ref(false);
const isOnline = ref(navigator.onLine);
const isDbLoading = ref(false);

const homeContext = inject('dataHomeAddress', null);

let db = null;

const closeResults = () => {
  setTimeout(() => {
    showResults.value = false;
  }, 150);
};

//Select an address from the results and update the model
const selectAddress = (place) => {
  isSelecting.value = true;
  const label = place.display_name || place.l;
  query.value = label;
  address.value = {
    label: label,
    lat: place.lat,
    lon: place.lon
  };

  results.value = [];
  showResults.value = false;
};

// Listen to online/offline events to update the isOnline state
const updateOnlineStatus = () => {
  isOnline.value = navigator.onLine;
  if (!isOnline.value) {
    initOfflineDB();
  }
};

// Initialize the SQLite database for offline use
const initOfflineDB = async () => {
  // if (db || isDbLoading.value) return;
  // isDbLoading.value = true;
  // try {
  //   const SQL = await initSqlJs({
  //     locateFile: (file) => file.endsWith('.wasm') ? '/sql-wasm.wasm' : file
  //   });
  //   const response = await fetch('/belgium.sqlite');
  //   const buf = await response.arrayBuffer();
  //   db = new SQL.Database(new Uint8Array(buf));
  // } catch (err) {
  //   console.error("Échec du chargement de la base locale:", err);
  // } finally {
  //   isDbLoading.value = false;
};

onMounted(() => {
  window.addEventListener('online', updateOnlineStatus);
  window.addEventListener('offline', updateOnlineStatus);
});

onUnmounted(() => {
  if (debounceTimeout) clearTimeout(debounceTimeout);
  window.removeEventListener('online', updateOnlineStatus);
  window.removeEventListener('offline', updateOnlineStatus);
});

// Watch the query and fetch results from Nominatim API
watch(query, async (value) => {
  if (isSelecting.value) {
    isSelecting.value = false;
    return;
  }

  // Si on vide l'input, on vide l'objet adresse
  if (!value) {
    address.value = {};
    results.value = [];
    return;
  }

  clearTimeout(debounceTimeout);
  if (value.length < 3) return;

  if (!isOnline.value) {
    if (!db) await initOfflineDB();
    if (db) {
      const res = db.exec("SELECT l, lat, lon FROM addresses WHERE l LIKE ? LIMIT 10", [`${value}%`]);
      if (res.length > 0) {
        results.value = res[0].values.map(row => ({l: row[0], lat: row[1], lon: row[2]}));
        showResults.value = true;
      }
    }
    return;
  }

  debounceTimeout = setTimeout(async () => {
    try {
      const res = await fetch(
          `https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(value)}&addressdetails=1&limit=5&countrycodes=be,lu&accept-language=fr`
      );
      const data = await res.json();
      results.value = data;
      showResults.value = true;
    } catch (e) {
      console.error(e);
    }
  }, 300);
});

// Sync the query string with the model value (for external changes)
watch(() => address.value, (newVal) => {
  if (newVal && typeof newVal === 'object') {
    const newLabel = newVal.label || newVal.display_name || newVal.l || '';
    if (newLabel !== query.value) {
      isSelecting.value = true;
      query.value = newLabel;
    }
  }
}, {immediate: true, deep: true});
</script>

<template>
  <div class="relative w-full">
    <label class="text-[10px] font-black uppercase text-slate-600 block mb-1 ml-1" for="job">{{ props.labelName }}</label>
    <input
        v-model="query"
        type="text"
        :placeholder="placeholder"
        class="w-full bg-slate-100 border-none rounded-xl text-sm font-bold text-slate-600 focus:ring-2 focus:ring-slate-900 p-3"
        @focus="showResults = true"
        @blur="closeResults"
    />
    <ul
        v-if="showResults && results.length"
        class="absolute z-10 mt-1 w-full bg-white border border-slate-200 rounded-xl shadow-lg max-h-60 overflow-auto"
    >
      <li
          v-for="(result, index) in results"
          :key="index"
          @mousedown="selectAddress(result)"
          class="p-2 text-sm cursor-pointer hover:bg-slate-100"
      >
        {{ result.display_name || result.l }}
      </li>
    </ul>
  </div>
</template>
