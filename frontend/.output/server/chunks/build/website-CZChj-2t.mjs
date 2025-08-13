import { watch, mergeProps, unref, useSSRContext } from 'vue';
import { ssrRenderAttrs, ssrRenderAttr, ssrRenderClass, ssrInterpolate } from 'vue/server-renderer';
import { h as useWebsiteSettingsStore, d as useSettingsStore, s as storeToRefs } from './server.mjs';
import '../_/nitro.mjs';
import 'node:http';
import 'node:https';
import 'node:events';
import 'node:buffer';
import 'node:fs';
import 'node:url';
import '@iconify/utils';
import 'node:crypto';
import 'consola';
import 'node:path';
import 'vue-router';
import '@vueuse/core';
import 'tailwind-merge';
import '@iconify/vue';
import '@heroicons/vue/24/outline';
import '../routes/renderer.mjs';
import 'vue-bundle-renderer/runtime';
import 'unhead/server';
import 'devalue';
import 'unhead/utils';
import 'unhead/plugins';

const _sfc_main = {
  __name: "website",
  __ssrInlineRender: true,
  setup(__props) {
    const websiteSettingsStore = useWebsiteSettingsStore();
    useSettingsStore();
    const {
      websiteName,
      websiteSecondaryName,
      websiteTitle,
      showLogo,
      logoUrl,
      faviconUrl,
      enableMultilingual,
      enableSearch,
      enableNotifications,
      showFooter,
      enableDarkMode
    } = storeToRefs(websiteSettingsStore);
    watch([websiteName, websiteSecondaryName, websiteTitle], () => {
      websiteSettingsStore.saveSettings();
    }, { deep: true });
    return (_ctx, _push, _parent, _attrs) => {
      _push(`<div${ssrRenderAttrs(mergeProps({ class: "space-y-6" }, _attrs))}><div class="bg-white dark:bg-gray-800 rounded-lg-custom shadow-sm p-6"><h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-6"> \u7DB2\u7AD9\u8A2D\u5B9A </h2><div class="mb-8"><h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4"> \u7DB2\u7AD9\u8B58\u5225 </h3><div class="space-y-4"><div><label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2"> \u4E3B\u8981\u7DB2\u7AD9\u540D\u7A31 </label><input${ssrRenderAttr("value", unref(websiteName))} type="text" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent dark:bg-gray-700 dark:text-white" placeholder="\u5C08\u6848\u7BA1\u7406\u7CFB\u7D71"></div><div><label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2"> \u6B21\u8981\u7DB2\u7AD9\u540D\u7A31 </label><input${ssrRenderAttr("value", unref(websiteSecondaryName))} type="text" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent dark:bg-gray-700 dark:text-white" placeholder="Project Management"></div><div><label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2"> \u7DB2\u7AD9\u6A19\u984C\uFF08\u700F\u89BD\u5668\u6A19\u7C64\u9801\u986F\u793A\uFF09 </label><input${ssrRenderAttr("value", unref(websiteTitle))} type="text" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent dark:bg-gray-700 dark:text-white" placeholder="\u5C08\u6848\u7BA1\u7406\u7CFB\u7D71"></div></div></div><div class="mb-8"><h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4"> \u6A19\u8A8C\u8A2D\u5B9A </h3><div class="space-y-4"><div class="flex items-center justify-between p-4 border border-gray-200 dark:border-gray-700 rounded-lg"><div><h4 class="font-medium text-gray-900 dark:text-white">\u4F7F\u7528\u6A19\u8A8C</h4><p class="text-sm text-gray-600 dark:text-gray-400">\u4F7F\u7528\u6A19\u8A8C\u53D6\u4EE3\u6587\u5B57\u540D\u7A31\u986F\u793A</p></div><button class="${ssrRenderClass([[
        unref(showLogo) ? "bg-primary-500" : "bg-gray-200 dark:bg-gray-700"
      ], "relative inline-flex h-6 w-11 items-center rounded-full transition-colors focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2"])}"><span class="${ssrRenderClass([[
        unref(showLogo) ? "translate-x-6" : "translate-x-1"
      ], "inline-block h-4 w-4 transform rounded-full bg-white transition-transform"])}"></span></button></div>`);
      if (unref(showLogo) || unref(logoUrl)) {
        _push(`<div><label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2"> \u6A19\u8A8C\u5716\u7247 </label><div class="flex items-center space-x-4">`);
        if (unref(logoUrl)) {
          _push(`<div class="flex-shrink-0"><img${ssrRenderAttr("src", unref(logoUrl))} alt="Logo Preview" class="w-16 h-16 object-contain border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-700"></div>`);
        } else {
          _push(`<!---->`);
        }
        _push(`<div class="flex-1"><input type="file" accept="image/*" class="hidden"><button class="px-4 py-2 bg-primary-500 text-white rounded-lg hover:bg-primary-600 transition-colors duration-200">${ssrInterpolate(unref(logoUrl) ? "\u66F4\u63DB\u6A19\u8A8C" : "\u4E0A\u50B3\u6A19\u8A8C")}</button><p class="text-xs text-gray-500 dark:text-gray-400 mt-1"> \u652F\u63F4 JPG\u3001PNG\u3001SVG \u683C\u5F0F\uFF0C\u5EFA\u8B70\u5C3A\u5BF8 200x50px </p></div></div></div>`);
      } else {
        _push(`<!---->`);
      }
      _push(`</div></div><div class="mb-8"><h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4"> \u7DB2\u7AD9\u5716\u793A\u8A2D\u5B9A </h3><div class="space-y-4"><div><label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2"> Favicon\uFF08\u7DB2\u7AD9\u5716\u793A\uFF09 </label><div class="flex items-center space-x-4"><div class="flex-shrink-0"><img${ssrRenderAttr("src", unref(faviconUrl))} alt="Favicon Preview" class="w-8 h-8 object-contain border border-gray-300 dark:border-gray-600 rounded bg-gray-50 dark:bg-gray-700"></div><div class="flex-1"><input type="file" accept="image/x-icon,image/png,image/svg+xml" class="hidden"><button class="px-4 py-2 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-200"> \u66F4\u63DB Favicon </button><p class="text-xs text-gray-500 dark:text-gray-400 mt-1"> \u652F\u63F4 ICO\u3001PNG\u3001SVG \u683C\u5F0F\uFF0C\u5EFA\u8B70\u5C3A\u5BF8 32x32px </p></div></div></div></div></div><div class="mb-8"><h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4"> \u529F\u80FD\u8A2D\u5B9A </h3><div class="space-y-4"><div class="flex items-center justify-between p-4 border border-gray-200 dark:border-gray-700 rounded-lg"><div><h4 class="font-medium text-gray-900 dark:text-white">\u591A\u8A9E\u8A00\u652F\u63F4</h4><p class="text-sm text-gray-600 dark:text-gray-400">\u555F\u7528\u591A\u8A9E\u8A00\u5207\u63DB\u529F\u80FD</p></div><button class="${ssrRenderClass([[
        unref(enableMultilingual) ? "bg-primary-500" : "bg-gray-200 dark:bg-gray-700"
      ], "relative inline-flex h-6 w-11 items-center rounded-full transition-colors focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2"])}"><span class="${ssrRenderClass([[
        unref(enableMultilingual) ? "translate-x-6" : "translate-x-1"
      ], "inline-block h-4 w-4 transform rounded-full bg-white transition-transform"])}"></span></button></div><div class="flex items-center justify-between p-4 border border-gray-200 dark:border-gray-700 rounded-lg"><div><h4 class="font-medium text-gray-900 dark:text-white">\u6DF1\u8272\u6A21\u5F0F</h4><p class="text-sm text-gray-600 dark:text-gray-400">\u5141\u8A31\u7528\u6236\u5207\u63DB\u6DF1\u8272/\u6DFA\u8272\u4E3B\u984C</p></div><button class="${ssrRenderClass([[
        unref(enableDarkMode) ? "bg-primary-500" : "bg-gray-200 dark:bg-gray-700"
      ], "relative inline-flex h-6 w-11 items-center rounded-full transition-colors focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2"])}"><span class="${ssrRenderClass([[
        unref(enableDarkMode) ? "translate-x-6" : "translate-x-1"
      ], "inline-block h-4 w-4 transform rounded-full bg-white transition-transform"])}"></span></button></div><div class="flex items-center justify-between p-4 border border-gray-200 dark:border-gray-700 rounded-lg"><div><h4 class="font-medium text-gray-900 dark:text-white">\u641C\u5C0B\u529F\u80FD</h4><p class="text-sm text-gray-600 dark:text-gray-400">\u5728\u9802\u90E8\u5C0E\u822A\u6B04\u986F\u793A\u641C\u5C0B\u6309\u9215</p></div><button class="${ssrRenderClass([[
        unref(enableSearch) ? "bg-primary-500" : "bg-gray-200 dark:bg-gray-700"
      ], "relative inline-flex h-6 w-11 items-center rounded-full transition-colors focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2"])}"><span class="${ssrRenderClass([[
        unref(enableSearch) ? "translate-x-6" : "translate-x-1"
      ], "inline-block h-4 w-4 transform rounded-full bg-white transition-transform"])}"></span></button></div><div class="flex items-center justify-between p-4 border border-gray-200 dark:border-gray-700 rounded-lg"><div><h4 class="font-medium text-gray-900 dark:text-white">\u901A\u77E5\u529F\u80FD</h4><p class="text-sm text-gray-600 dark:text-gray-400">\u5728\u9802\u90E8\u5C0E\u822A\u6B04\u986F\u793A\u901A\u77E5\u9234\u943A</p></div><button class="${ssrRenderClass([[
        unref(enableNotifications) ? "bg-primary-500" : "bg-gray-200 dark:bg-gray-700"
      ], "relative inline-flex h-6 w-11 items-center rounded-full transition-colors focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2"])}"><span class="${ssrRenderClass([[
        unref(enableNotifications) ? "translate-x-6" : "translate-x-1"
      ], "inline-block h-4 w-4 transform rounded-full bg-white transition-transform"])}"></span></button></div><div class="flex items-center justify-between p-4 border border-gray-200 dark:border-gray-700 rounded-lg"><div><h4 class="font-medium text-gray-900 dark:text-white">\u9801\u5C3E\u986F\u793A</h4><p class="text-sm text-gray-600 dark:text-gray-400">\u5728\u9801\u9762\u5E95\u90E8\u986F\u793A\u9801\u5C3E\u8CC7\u8A0A</p></div><button class="${ssrRenderClass([[
        unref(showFooter) ? "bg-primary-500" : "bg-gray-200 dark:bg-gray-700"
      ], "relative inline-flex h-6 w-11 items-center rounded-full transition-colors focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2"])}"><span class="${ssrRenderClass([[
        unref(showFooter) ? "translate-x-6" : "translate-x-1"
      ], "inline-block h-4 w-4 transform rounded-full bg-white transition-transform"])}"></span></button></div></div></div><div class="mb-8"><h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4"> \u9810\u89BD </h3><div class="p-6 border border-gray-200 dark:border-gray-700 rounded-lg bg-gray-50 dark:bg-gray-700"><div class="flex items-center space-x-3 mb-4">`);
      if (unref(showLogo) && unref(logoUrl)) {
        _push(`<div class="flex-shrink-0"><img${ssrRenderAttr("src", unref(logoUrl))} alt="Logo" class="h-8 object-contain"></div>`);
      } else {
        _push(`<div class="flex flex-col"><span class="font-bold text-lg text-gray-900 dark:text-white">${ssrInterpolate(unref(websiteName))}</span>`);
        if (unref(websiteSecondaryName)) {
          _push(`<span class="text-sm text-gray-600 dark:text-gray-400">${ssrInterpolate(unref(websiteSecondaryName))}</span>`);
        } else {
          _push(`<!---->`);
        }
        _push(`</div>`);
      }
      _push(`</div><p class="text-sm text-gray-600 dark:text-gray-400"> \u9801\u9762\u6A19\u984C\uFF1A${ssrInterpolate(unref(websiteTitle))}</p></div></div><div class="flex justify-end space-x-3"><button class="px-4 py-2 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-200"> \u91CD\u7F6E\u70BA\u9810\u8A2D\u503C </button><button class="px-4 py-2 bg-primary-500 text-white rounded-lg hover:bg-primary-600 transition-colors duration-200"> \u5132\u5B58\u8A2D\u5B9A </button></div></div></div>`);
    };
  }
};
const _sfc_setup = _sfc_main.setup;
_sfc_main.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("pages/settings/website.vue");
  return _sfc_setup ? _sfc_setup(props, ctx) : void 0;
};

export { _sfc_main as default };
//# sourceMappingURL=website-CZChj-2t.mjs.map
