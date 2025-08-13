import { f as __nuxt_component_0$2 } from './server.mjs';
import { ref, computed, watch, mergeProps, withCtx, unref, createVNode, createTextVNode, useSSRContext } from 'vue';
import { ssrRenderAttrs, ssrRenderComponent, ssrRenderAttr, ssrIncludeBooleanAttr, ssrLooseContain, ssrLooseEqual, ssrRenderList, ssrInterpolate, ssrRenderClass } from 'vue/server-renderer';
import { PlusIcon, PencilIcon, TrashIcon } from '@heroicons/vue/24/outline';
import { u as useProjects } from './useProjects-BpvOU6mv.mjs';
import { u as useClients } from './useClients-NUI8b-Oz.mjs';
import { u as useCurrency } from './useCurrency-By3xbm0s.mjs';
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
  __name: "index",
  __ssrInlineRender: true,
  setup(__props) {
    useProjects();
    useClients();
    const { formatTWD } = useCurrency();
    const projects = ref([]);
    const searchQuery = ref("");
    const filterCategory = ref("");
    const filterStatus = ref("");
    const filterClientId = ref("");
    const loading = ref(false);
    const error = ref(null);
    const clients = ref([]);
    ref(false);
    const filteredProjects = computed(() => {
      return projects.value.filter((project) => {
        const matchesSearch = project.name.toLowerCase().includes(searchQuery.value.toLowerCase()) || project.description.toLowerCase().includes(searchQuery.value.toLowerCase());
        const matchesCategory = !filterCategory.value || project.category === filterCategory.value;
        const matchesStatus = !filterStatus.value || project.status === filterStatus.value;
        const matchesClient = !filterClientId.value || project.client_id && project.client_id.toString() === filterClientId.value.toString();
        return matchesSearch && matchesCategory && matchesStatus && matchesClient;
      });
    });
    const getCategoryLabel = (category) => {
      const labels = {
        website: "\u7DB2\u7AD9",
        script: "\u8173\u672C",
        server: "\u4F3A\u670D\u5668",
        custom: "\u81EA\u8A02"
      };
      return labels[category] || category;
    };
    const getCategoryClass = (category) => {
      const classes = {
        website: "bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200",
        script: "bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200",
        server: "bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200",
        custom: "bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200"
      };
      return classes[category] || "bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200";
    };
    const getStatusLabel = (status) => {
      const labels = {
        contacted: "\u5DF2\u63A5\u6D3D",
        in_progress: "\u9032\u884C\u4E2D",
        completed: "\u5DF2\u5B8C\u6210",
        paid: "\u5DF2\u6536\u6B3E"
      };
      return labels[status] || status;
    };
    const getStatusClass = (status) => {
      const classes = {
        contacted: "bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200",
        in_progress: "bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200",
        completed: "bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200",
        paid: "bg-emerald-100 text-emerald-800 dark:bg-emerald-900 dark:text-emerald-200"
      };
      return classes[status] || "bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200";
    };
    const formatDate = (dateString) => {
      if (!dateString) return "\u672A\u8A2D\u5B9A";
      return new Date(dateString).toLocaleDateString("zh-TW");
    };
    watch([searchQuery, filterCategory, filterStatus, filterClientId], () => {
    });
    return (_ctx, _push, _parent, _attrs) => {
      const _component_NuxtLink = __nuxt_component_0$2;
      _push(`<div${ssrRenderAttrs(mergeProps({ class: "space-y-6" }, _attrs))}><div class="flex justify-between items-center"><div><h1 class="text-2xl font-bold text-gray-900 dark:text-white">\u5C08\u6848\u7BA1\u7406</h1><p class="text-gray-600 dark:text-gray-300">\u7BA1\u7406\u6240\u6709\u5C08\u6848\u8CC7\u8A0A</p></div>`);
      _push(ssrRenderComponent(_component_NuxtLink, {
        to: "/projects/create",
        class: "inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500"
      }, {
        default: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(ssrRenderComponent(unref(PlusIcon), { class: "w-4 h-4 mr-2" }, null, _parent2, _scopeId));
            _push2(` \u65B0\u589E\u5C08\u6848 `);
          } else {
            return [
              createVNode(unref(PlusIcon), { class: "w-4 h-4 mr-2" }),
              createTextVNode(" \u65B0\u589E\u5C08\u6848 ")
            ];
          }
        }),
        _: 1
      }, _parent));
      _push(`</div><div class="bg-white dark:bg-gray-800 rounded-lg-custom shadow-sm p-6"><div class="grid grid-cols-1 md:grid-cols-5 gap-4"><div><label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">\u641C\u5C0B\u5C08\u6848</label><input${ssrRenderAttr("value", unref(searchQuery))} type="text" placeholder="\u641C\u5C0B\u5C08\u6848\u540D\u7A31..." class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-white"></div><div><label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">\u696D\u4E3B</label><select class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-white"><option value=""${ssrIncludeBooleanAttr(Array.isArray(unref(filterClientId)) ? ssrLooseContain(unref(filterClientId), "") : ssrLooseEqual(unref(filterClientId), "")) ? " selected" : ""}>\u5168\u90E8\u696D\u4E3B</option><!--[-->`);
      ssrRenderList(unref(clients), (client) => {
        _push(`<option${ssrRenderAttr("value", client == null ? void 0 : client.id)}${ssrIncludeBooleanAttr(Array.isArray(unref(filterClientId)) ? ssrLooseContain(unref(filterClientId), client == null ? void 0 : client.id) : ssrLooseEqual(unref(filterClientId), client == null ? void 0 : client.id)) ? " selected" : ""}>${ssrInterpolate((client == null ? void 0 : client.name) || "\u672A\u77E5\u696D\u4E3B")}</option>`);
      });
      _push(`<!--]--></select></div><div><label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">\u5C08\u6848\u985E\u5225</label><select class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-white"><option value=""${ssrIncludeBooleanAttr(Array.isArray(unref(filterCategory)) ? ssrLooseContain(unref(filterCategory), "") : ssrLooseEqual(unref(filterCategory), "")) ? " selected" : ""}>\u5168\u90E8\u985E\u5225</option><option value="website"${ssrIncludeBooleanAttr(Array.isArray(unref(filterCategory)) ? ssrLooseContain(unref(filterCategory), "website") : ssrLooseEqual(unref(filterCategory), "website")) ? " selected" : ""}>\u7DB2\u7AD9</option><option value="script"${ssrIncludeBooleanAttr(Array.isArray(unref(filterCategory)) ? ssrLooseContain(unref(filterCategory), "script") : ssrLooseEqual(unref(filterCategory), "script")) ? " selected" : ""}>\u8173\u672C</option><option value="server"${ssrIncludeBooleanAttr(Array.isArray(unref(filterCategory)) ? ssrLooseContain(unref(filterCategory), "server") : ssrLooseEqual(unref(filterCategory), "server")) ? " selected" : ""}>\u4F3A\u670D\u5668</option><option value="custom"${ssrIncludeBooleanAttr(Array.isArray(unref(filterCategory)) ? ssrLooseContain(unref(filterCategory), "custom") : ssrLooseEqual(unref(filterCategory), "custom")) ? " selected" : ""}>\u81EA\u8A02</option></select></div><div><label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">\u5C08\u6848\u72C0\u614B</label><select class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-white"><option value=""${ssrIncludeBooleanAttr(Array.isArray(unref(filterStatus)) ? ssrLooseContain(unref(filterStatus), "") : ssrLooseEqual(unref(filterStatus), "")) ? " selected" : ""}>\u5168\u90E8\u72C0\u614B</option><option value="contacted"${ssrIncludeBooleanAttr(Array.isArray(unref(filterStatus)) ? ssrLooseContain(unref(filterStatus), "contacted") : ssrLooseEqual(unref(filterStatus), "contacted")) ? " selected" : ""}>\u5DF2\u63A5\u6D3D</option><option value="in_progress"${ssrIncludeBooleanAttr(Array.isArray(unref(filterStatus)) ? ssrLooseContain(unref(filterStatus), "in_progress") : ssrLooseEqual(unref(filterStatus), "in_progress")) ? " selected" : ""}>\u9032\u884C\u4E2D</option><option value="completed"${ssrIncludeBooleanAttr(Array.isArray(unref(filterStatus)) ? ssrLooseContain(unref(filterStatus), "completed") : ssrLooseEqual(unref(filterStatus), "completed")) ? " selected" : ""}>\u5DF2\u5B8C\u6210</option><option value="paid"${ssrIncludeBooleanAttr(Array.isArray(unref(filterStatus)) ? ssrLooseContain(unref(filterStatus), "paid") : ssrLooseEqual(unref(filterStatus), "paid")) ? " selected" : ""}>\u5DF2\u6536\u6B3E</option></select></div><div class="flex items-end"><button class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500"> \u6E05\u9664\u7BE9\u9078 </button></div></div></div>`);
      if (unref(loading)) {
        _push(`<div class="text-center py-12"><div class="inline-block h-8 w-8 animate-spin rounded-full border-4 border-solid border-current border-r-transparent align-[-0.125em] motion-reduce:animate-[spin_1.5s_linear_infinite]" role="status"><span class="!absolute !-m-px !h-px !w-px !overflow-hidden !whitespace-nowrap !border-0 !p-0 ![clip:rect(0,0,0,0)]">\u8F09\u5165\u4E2D...</span></div><p class="mt-2 text-gray-500 dark:text-gray-400">\u6B63\u5728\u8F09\u5165\u5C08\u6848\u8CC7\u6599...</p></div>`);
      } else if (unref(error)) {
        _push(`<div class="bg-red-50 dark:bg-red-900/50 border border-red-200 dark:border-red-700 rounded-md p-4"><div class="flex"><div class="flex-shrink-0"><svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path></svg></div><div class="ml-3"><p class="text-sm font-medium text-red-800 dark:text-red-200">${ssrInterpolate(unref(error))}</p><button class="mt-2 text-sm text-red-600 dark:text-red-400 hover:text-red-800 dark:hover:text-red-200 underline"> \u91CD\u65B0\u8F09\u5165 </button></div></div></div>`);
      } else {
        _push(`<div class="bg-white dark:bg-gray-800 rounded-lg-custom shadow-sm overflow-hidden"><div class="overflow-x-auto"><table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700"><thead class="bg-gray-50 dark:bg-gray-900"><tr><th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider"> \u5C08\u6848\u540D\u7A31 </th><th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider"> \u696D\u4E3B </th><th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider"> \u985E\u5225 </th><th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider"> \u91D1\u984D </th><th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider"> \u72C0\u614B </th><th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider"> \u63A5\u6D3D\u65E5\u671F </th><th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider"> \u64CD\u4F5C </th></tr></thead><tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700"><!--[-->`);
        ssrRenderList(unref(filteredProjects), (project) => {
          var _a;
          _push(`<tr class="hover:bg-gray-50 dark:hover:bg-gray-700"><td class="px-6 py-4 whitespace-nowrap"><div><div class="text-sm font-medium text-gray-900 dark:text-white">${ssrInterpolate((project == null ? void 0 : project.name) || "\u672A\u77E5\u5C08\u6848")}</div><div class="text-sm text-gray-500 dark:text-gray-400">${ssrInterpolate((project == null ? void 0 : project.description) || "\u7121\u63CF\u8FF0")}</div></div></td><td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">${ssrInterpolate(((_a = project == null ? void 0 : project.client) == null ? void 0 : _a.name) || "\u672A\u77E5\u696D\u4E3B")}</td><td class="px-6 py-4 whitespace-nowrap"><span class="${ssrRenderClass([getCategoryClass(project == null ? void 0 : project.category), "inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"])}">${ssrInterpolate(getCategoryLabel(project == null ? void 0 : project.category))}</span></td><td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">${ssrInterpolate(unref(formatTWD)((project == null ? void 0 : project.amount) || 0))}</td><td class="px-6 py-4 whitespace-nowrap"><span class="${ssrRenderClass([getStatusClass(project == null ? void 0 : project.status), "inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"])}">${ssrInterpolate(getStatusLabel(project == null ? void 0 : project.status))}</span></td><td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">${ssrInterpolate(formatDate(project == null ? void 0 : project.contact_date))}</td><td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium"><div class="flex justify-end space-x-2"><button class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400">`);
          _push(ssrRenderComponent(unref(PencilIcon), { class: "w-4 h-4" }, null, _parent));
          _push(`</button><button class="text-red-600 hover:text-red-900 dark:text-red-400"${ssrIncludeBooleanAttr(!(project == null ? void 0 : project.id)) ? " disabled" : ""}>`);
          _push(ssrRenderComponent(unref(TrashIcon), { class: "w-4 h-4" }, null, _parent));
          _push(`</button></div></td></tr>`);
        });
        _push(`<!--]--></tbody></table></div></div>`);
      }
      _push(`</div>`);
    };
  }
};
const _sfc_setup = _sfc_main.setup;
_sfc_main.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("pages/projects/index.vue");
  return _sfc_setup ? _sfc_setup(props, ctx) : void 0;
};

export { _sfc_main as default };
//# sourceMappingURL=index-DixGS72r.mjs.map
