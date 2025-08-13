import { ref, mergeProps, unref, useSSRContext } from 'vue';
import { ssrRenderAttrs, ssrRenderComponent, ssrInterpolate, ssrRenderAttr, ssrRenderList, ssrIncludeBooleanAttr, ssrLooseContain, ssrLooseEqual } from 'vue/server-renderer';
import { ArrowLeftIcon, PlusIcon, TrashIcon } from '@heroicons/vue/24/outline';
import { u as useClients } from './useClients-NUI8b-Oz.mjs';
import { e as useRoute } from './server.mjs';
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
import '../routes/renderer.mjs';
import 'vue-bundle-renderer/runtime';
import 'unhead/server';
import 'devalue';
import 'unhead/utils';
import 'unhead/plugins';

const _sfc_main = {
  __name: "[id]",
  __ssrInlineRender: true,
  setup(__props) {
    useClients();
    useRoute();
    const loading = ref(true);
    const error = ref(null);
    const isSubmitting = ref(false);
    const form = ref({
      name: "",
      how_we_met: "",
      notes: "",
      contacts: [
        {
          type: "",
          value: "",
          is_primary: true
        }
      ]
    });
    return (_ctx, _push, _parent, _attrs) => {
      _push(`<div${ssrRenderAttrs(mergeProps({ class: "space-y-6" }, _attrs))}><div class="flex items-center space-x-4"><button class="p-2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">`);
      _push(ssrRenderComponent(unref(ArrowLeftIcon), { class: "w-5 h-5" }, null, _parent));
      _push(`</button><div><h1 class="text-2xl font-bold text-gray-900 dark:text-white">\u7DE8\u8F2F\u696D\u4E3B</h1><p class="text-gray-600 dark:text-gray-300">\u4FEE\u6539\u696D\u4E3B\u8CC7\u6599</p></div></div>`);
      if (unref(loading)) {
        _push(`<div class="text-center py-12"><div class="inline-block h-8 w-8 animate-spin rounded-full border-4 border-solid border-current border-r-transparent align-[-0.125em] motion-reduce:animate-[spin_1.5s_linear_infinite]" role="status"><span class="!absolute !-m-px !h-px !w-px !overflow-hidden !whitespace-nowrap !border-0 !p-0 ![clip:rect(0,0,0,0)]">\u8F09\u5165\u4E2D...</span></div><p class="mt-2 text-gray-500 dark:text-gray-400">\u6B63\u5728\u8F09\u5165\u696D\u4E3B\u8CC7\u6599...</p></div>`);
      } else if (unref(error)) {
        _push(`<div class="bg-red-50 dark:bg-red-900/50 border border-red-200 dark:border-red-700 rounded-md p-4"><div class="flex"><div class="flex-shrink-0"><svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path></svg></div><div class="ml-3"><p class="text-sm font-medium text-red-800 dark:text-red-200">${ssrInterpolate(unref(error))}</p><button class="mt-2 text-sm text-red-600 dark:text-red-400 hover:text-red-800 dark:hover:text-red-200 underline"> \u91CD\u65B0\u8F09\u5165 </button></div></div></div>`);
      } else {
        _push(`<div class="bg-white dark:bg-gray-800 rounded-lg-custom shadow-sm"><form class="p-6 space-y-6"><div><h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">\u57FA\u672C\u8CC7\u8A0A</h3><div class="grid grid-cols-1 md:grid-cols-2 gap-6"><div><label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2"> \u696D\u4E3B\u7A31\u547C <span class="text-red-500">*</span></label><input id="name"${ssrRenderAttr("value", unref(form).name)} type="text" required class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-white" placeholder="\u8ACB\u8F38\u5165\u696D\u4E3B\u7A31\u547C"></div><div><label for="how_we_met" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2"> \u8A8D\u8B58\u65B9\u5F0F </label><input id="how_we_met"${ssrRenderAttr("value", unref(form).how_we_met)} type="text" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-white" placeholder="\u4F8B\u5982\uFF1A\u670B\u53CB\u4ECB\u7D39\u3001\u7DB2\u8DEF\u63A5\u6D3D..."></div></div></div><div><label for="notes" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2"> \u5099\u8A3B </label><textarea id="notes" rows="4" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-white" placeholder="\u8ACB\u8F38\u5165\u5099\u8A3B\u8CC7\u8A0A...">${ssrInterpolate(unref(form).notes)}</textarea></div><div><div class="flex justify-between items-center mb-4"><h3 class="text-lg font-medium text-gray-900 dark:text-white">\u806F\u7E6B\u65B9\u5F0F</h3><button type="button" class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-primary-700 bg-primary-100 hover:bg-primary-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 dark:bg-primary-900 dark:text-primary-300 dark:hover:bg-primary-800">`);
        _push(ssrRenderComponent(unref(PlusIcon), { class: "w-4 h-4 mr-1" }, null, _parent));
        _push(` \u65B0\u589E\u806F\u7E6B\u65B9\u5F0F </button></div><div class="space-y-4"><!--[-->`);
        ssrRenderList(unref(form).contacts, (contact, index) => {
          _push(`<div class="flex items-start space-x-4 p-4 border border-gray-200 dark:border-gray-700 rounded-md"><div class="flex-1 grid grid-cols-1 md:grid-cols-3 gap-4"><div><label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1"> \u985E\u578B </label><select class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-white text-sm"><option value=""${ssrIncludeBooleanAttr(Array.isArray(contact.type) ? ssrLooseContain(contact.type, "") : ssrLooseEqual(contact.type, "")) ? " selected" : ""}>\u8ACB\u9078\u64C7</option><option value="phone"${ssrIncludeBooleanAttr(Array.isArray(contact.type) ? ssrLooseContain(contact.type, "phone") : ssrLooseEqual(contact.type, "phone")) ? " selected" : ""}>\u96FB\u8A71</option><option value="mobile"${ssrIncludeBooleanAttr(Array.isArray(contact.type) ? ssrLooseContain(contact.type, "mobile") : ssrLooseEqual(contact.type, "mobile")) ? " selected" : ""}>\u624B\u6A5F</option><option value="email"${ssrIncludeBooleanAttr(Array.isArray(contact.type) ? ssrLooseContain(contact.type, "email") : ssrLooseEqual(contact.type, "email")) ? " selected" : ""}>\u96FB\u5B50\u90F5\u4EF6</option><option value="line"${ssrIncludeBooleanAttr(Array.isArray(contact.type) ? ssrLooseContain(contact.type, "line") : ssrLooseEqual(contact.type, "line")) ? " selected" : ""}>LINE</option><option value="wechat"${ssrIncludeBooleanAttr(Array.isArray(contact.type) ? ssrLooseContain(contact.type, "wechat") : ssrLooseEqual(contact.type, "wechat")) ? " selected" : ""}>WeChat</option><option value="telegram"${ssrIncludeBooleanAttr(Array.isArray(contact.type) ? ssrLooseContain(contact.type, "telegram") : ssrLooseEqual(contact.type, "telegram")) ? " selected" : ""}>Telegram</option><option value="other"${ssrIncludeBooleanAttr(Array.isArray(contact.type) ? ssrLooseContain(contact.type, "other") : ssrLooseEqual(contact.type, "other")) ? " selected" : ""}>\u5176\u4ED6</option></select></div><div><label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1"> \u806F\u7E6B\u8CC7\u8A0A </label><input${ssrRenderAttr("value", contact.value)} type="text" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-white text-sm" placeholder="\u8ACB\u8F38\u5165\u806F\u7E6B\u8CC7\u8A0A"></div><div class="flex items-center space-x-4"><label class="flex items-center"><input${ssrIncludeBooleanAttr(Array.isArray(contact.is_primary) ? ssrLooseContain(contact.is_primary, null) : contact.is_primary) ? " checked" : ""} type="checkbox" class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 rounded"><span class="ml-2 text-sm text-gray-700 dark:text-gray-300">\u4E3B\u8981\u806F\u7E6B\u65B9\u5F0F</span></label></div></div><button type="button" class="p-2 text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-200"${ssrIncludeBooleanAttr(unref(form).contacts.length <= 1) ? " disabled" : ""}>`);
          _push(ssrRenderComponent(unref(TrashIcon), { class: "w-4 h-4" }, null, _parent));
          _push(`</button></div>`);
        });
        _push(`<!--]--></div></div><div class="flex justify-end space-x-4 pt-6 border-t border-gray-200 dark:border-gray-700"><button type="button" class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500"> \u53D6\u6D88 </button><button type="submit"${ssrIncludeBooleanAttr(unref(isSubmitting)) ? " disabled" : ""} class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 disabled:opacity-50 disabled:cursor-not-allowed">`);
        if (unref(isSubmitting)) {
          _push(`<span>\u66F4\u65B0\u4E2D...</span>`);
        } else {
          _push(`<span>\u66F4\u65B0\u696D\u4E3B</span>`);
        }
        _push(`</button></div></form></div>`);
      }
      _push(`</div>`);
    };
  }
};
const _sfc_setup = _sfc_main.setup;
_sfc_main.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("pages/clients/[id].vue");
  return _sfc_setup ? _sfc_setup(props, ctx) : void 0;
};

export { _sfc_main as default };
//# sourceMappingURL=_id_-WwAblhoB.mjs.map
