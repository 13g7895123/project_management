import { _ as __nuxt_component_0$3, u as useApi } from './server.mjs';
import { ref, computed, mergeProps, unref, createVNode, resolveDynamicComponent, withCtx, useSSRContext } from 'vue';
import { ssrRenderAttrs, ssrRenderList, ssrRenderVNode, ssrInterpolate, ssrRenderComponent } from 'vue/server-renderer';
import { FolderIcon, ClockIcon, ChartBarIcon, CurrencyDollarIcon, UsersIcon } from '@heroicons/vue/24/outline';
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

const useDashboard = () => {
  const { get } = useApi();
  const getDashboardStats = async () => {
    return await get("/dashboard/stats");
  };
  const getRevenueStats = async (period = "month", params = {}) => {
    return await get(`/dashboard/revenue/${period}`, params);
  };
  const getProjectCountStats = async (period = "month", params = {}) => {
    return await get(`/dashboard/projects/${period}`, params);
  };
  const getClientStats = async (params = {}) => {
    return await get("/dashboard/clients", params);
  };
  const getRecentActivities = async (limit = 10) => {
    return await get("/dashboard/activities", { limit });
  };
  const getUpcomingDeadlines = async (days = 30) => {
    return await get("/dashboard/deadlines", { days });
  };
  const getMonthlyRevenueTrend = async (months = 12) => {
    return await get("/dashboard/revenue/trend", { months });
  };
  const getProjectStatusDistribution = async () => {
    return await get("/dashboard/projects/status-distribution");
  };
  const getCategoryRevenueBreakdown = async (period = "year") => {
    return await get("/dashboard/revenue/by-category", { period });
  };
  const getTopClientsByRevenue = async (limit = 10, period = "year") => {
    return await get("/dashboard/clients/top-revenue", { limit, period });
  };
  const getDailyStats = async (startDate, endDate) => {
    return await get("/dashboard/daily-stats", {
      start_date: startDate,
      end_date: endDate
    });
  };
  const getWeeklyStats = async (startDate, endDate) => {
    return await get("/dashboard/weekly-stats", {
      start_date: startDate,
      end_date: endDate
    });
  };
  const getYearlyStats = async (year) => {
    return await get("/dashboard/yearly-stats", { year });
  };
  return {
    getDashboardStats,
    getRevenueStats,
    getProjectCountStats,
    getClientStats,
    getRecentActivities,
    getUpcomingDeadlines,
    getMonthlyRevenueTrend,
    getProjectStatusDistribution,
    getCategoryRevenueBreakdown,
    getTopClientsByRevenue,
    getDailyStats,
    getWeeklyStats,
    getYearlyStats
  };
};
const _sfc_main = {
  __name: "index",
  __ssrInlineRender: true,
  setup(__props) {
    useDashboard();
    const { formatTWD } = useCurrency();
    ref(true);
    ref(null);
    const dashboardData = ref({});
    const activities = ref([]);
    const loadingChart = ref(true);
    const chartError = ref(null);
    ref([]);
    const stats = computed(() => [
      {
        name: "\u7E3D\u5C08\u6848\u6578",
        value: dashboardData.value.total_projects || 0,
        icon: "FolderIcon"
      },
      {
        name: "\u5DF2\u6536\u5165",
        value: formatTWD(dashboardData.value.total_revenue || 0),
        icon: "CurrencyDollarIcon"
      },
      {
        name: "\u9810\u671F\u7E3D\u6536\u5165",
        value: formatTWD(dashboardData.value.expected_revenue || 0),
        icon: "ChartBarIcon"
      },
      {
        name: "\u9032\u884C\u4E2D\u5C08\u6848",
        value: dashboardData.value.in_progress_projects || 0,
        icon: "ClockIcon"
      }
    ]);
    const iconComponents = {
      UsersIcon,
      CurrencyDollarIcon,
      ChartBarIcon,
      ClockIcon,
      FolderIcon
    };
    const getIcon = (iconName) => {
      return iconComponents[iconName] || ChartBarIcon;
    };
    return (_ctx, _push, _parent, _attrs) => {
      const _component_ClientOnly = __nuxt_component_0$3;
      _push(`<div${ssrRenderAttrs(mergeProps({ class: "space-y-6" }, _attrs))}><div class="bg-white dark:bg-gray-800 rounded-lg-custom shadow-sm p-6"><h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-4"> \u5C08\u6848\u7BA1\u7406\u5100\u8868\u677F </h2><p class="text-gray-600 dark:text-gray-300"> \u7BA1\u7406\u60A8\u7684\u5C08\u6848\u3001\u696D\u4E3B\u8CC7\u8A0A\uFF0C\u4E26\u8FFD\u8E64\u5C08\u6848\u9032\u5EA6\u548C\u6536\u5165\u7D71\u8A08\u3002 </p></div><div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6"><!--[-->`);
      ssrRenderList(unref(stats), (stat) => {
        _push(`<div class="bg-white dark:bg-gray-800 rounded-lg-custom shadow-sm p-6"><div class="flex items-center"><div class="p-3 rounded-lg bg-primary-100 dark:bg-primary-900">`);
        ssrRenderVNode(_push, createVNode(resolveDynamicComponent(getIcon(stat.icon)), { class: "w-6 h-6 text-primary-600 dark:text-primary-400" }, null), _parent);
        _push(`</div><div class="ml-4"><p class="text-sm font-medium text-gray-600 dark:text-gray-400">${ssrInterpolate(stat.name)}</p><p class="text-2xl font-bold text-gray-900 dark:text-white">${ssrInterpolate(stat.value)}</p></div></div></div>`);
      });
      _push(`<!--]--></div><div class="grid grid-cols-1 lg:grid-cols-2 gap-6"><div class="bg-white dark:bg-gray-800 rounded-lg-custom shadow-sm p-6"><h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4"> \u6536\u5165\u8DA8\u52E2 </h3>`);
      if (unref(loadingChart)) {
        _push(`<div class="h-64 bg-gray-100 dark:bg-gray-700 rounded-lg flex items-center justify-center"><div class="text-center"><div class="inline-block h-8 w-8 animate-spin rounded-full border-4 border-solid border-current border-r-transparent align-[-0.125em] motion-reduce:animate-[spin_1.5s_linear_infinite]" role="status"><span class="!absolute !-m-px !h-px !w-px !overflow-hidden !whitespace-nowrap !border-0 !p-0 ![clip:rect(0,0,0,0)]">\u8F09\u5165\u4E2D...</span></div><p class="mt-2 text-gray-500 dark:text-gray-400">\u8F09\u5165\u6536\u5165\u8DA8\u52E2...</p></div></div>`);
      } else if (unref(chartError)) {
        _push(`<div class="h-64 bg-gray-100 dark:bg-gray-700 rounded-lg flex items-center justify-center"><div class="text-center"><svg class="mx-auto h-12 w-12 text-red-400 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg><p class="text-red-500 dark:text-red-400 text-sm">${ssrInterpolate(unref(chartError))}</p><button class="mt-2 text-primary-600 hover:text-primary-700 text-sm underline">\u91CD\u8A66</button></div></div>`);
      } else {
        _push(`<div class="h-64">`);
        _push(ssrRenderComponent(_component_ClientOnly, null, {
          fallback: withCtx((_, _push2, _parent2, _scopeId) => {
            if (_push2) {
              _push2(`<div class="h-64 bg-gray-100 dark:bg-gray-700 rounded-lg flex items-center justify-center"${_scopeId}><div class="text-center"${_scopeId}><div class="inline-block h-8 w-8 animate-spin rounded-full border-4 border-solid border-current border-r-transparent align-[-0.125em] motion-reduce:animate-[spin_1.5s_linear_infinite]" role="status"${_scopeId}><span class="!absolute !-m-px !h-px !w-px !overflow-hidden !whitespace-nowrap !border-0 !p-0 ![clip:rect(0,0,0,0)]"${_scopeId}>\u8F09\u5165\u4E2D...</span></div><p class="mt-2 text-gray-500 dark:text-gray-400"${_scopeId}>\u8F09\u5165\u6536\u5165\u8DA8\u52E2...</p></div></div>`);
            } else {
              return [
                createVNode("div", { class: "h-64 bg-gray-100 dark:bg-gray-700 rounded-lg flex items-center justify-center" }, [
                  createVNode("div", { class: "text-center" }, [
                    createVNode("div", {
                      class: "inline-block h-8 w-8 animate-spin rounded-full border-4 border-solid border-current border-r-transparent align-[-0.125em] motion-reduce:animate-[spin_1.5s_linear_infinite]",
                      role: "status"
                    }, [
                      createVNode("span", { class: "!absolute !-m-px !h-px !w-px !overflow-hidden !whitespace-nowrap !border-0 !p-0 ![clip:rect(0,0,0,0)]" }, "\u8F09\u5165\u4E2D...")
                    ]),
                    createVNode("p", { class: "mt-2 text-gray-500 dark:text-gray-400" }, "\u8F09\u5165\u6536\u5165\u8DA8\u52E2...")
                  ])
                ])
              ];
            }
          })
        }, _parent));
        _push(`</div>`);
      }
      _push(`</div><div class="bg-white dark:bg-gray-800 rounded-lg-custom shadow-sm p-6"><h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4"> \u5C08\u6848\u52D5\u614B </h3>`);
      if (unref(activities) && unref(activities).length > 0) {
        _push(`<div class="space-y-4"><!--[-->`);
        ssrRenderList(unref(activities), (activity) => {
          _push(`<div class="flex items-start space-x-3"><div class="w-2 h-2 mt-2 bg-primary-500 rounded-full"></div><div class="flex-1"><p class="text-sm text-gray-900 dark:text-white">${ssrInterpolate((activity == null ? void 0 : activity.description) || "\u7121\u63CF\u8FF0")}</p><p class="text-xs text-gray-500 dark:text-gray-400">${ssrInterpolate((activity == null ? void 0 : activity.time) || "\u672A\u77E5\u6642\u9593")}</p></div></div>`);
        });
        _push(`<!--]--></div>`);
      } else {
        _push(`<div class="text-center py-8 text-gray-500 dark:text-gray-400"><svg class="mx-auto h-12 w-12 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg><p>\u66AB\u7121\u5C08\u6848\u52D5\u614B</p></div>`);
      }
      _push(`</div></div><div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6"><div class="bg-white dark:bg-gray-800 rounded-lg-custom shadow-sm p-6"><h4 class="text-sm font-medium text-gray-600 dark:text-gray-400 mb-2">\u5F85\u6536\u6B3E\u9805</h4><p class="text-2xl font-bold text-orange-600 dark:text-orange-400">${ssrInterpolate(unref(formatTWD)(unref(dashboardData).pending_revenue || 0))}</p><p class="text-xs text-gray-500 dark:text-gray-400 mt-1">\u5DF2\u5B8C\u6210\u5F85\u6536</p></div><div class="bg-white dark:bg-gray-800 rounded-lg-custom shadow-sm p-6"><h4 class="text-sm font-medium text-gray-600 dark:text-gray-400 mb-2">\u6F5B\u5728\u6536\u5165</h4><p class="text-2xl font-bold text-blue-600 dark:text-blue-400">${ssrInterpolate(unref(formatTWD)(unref(dashboardData).potential_revenue || 0))}</p><p class="text-xs text-gray-500 dark:text-gray-400 mt-1">\u9032\u884C\u4E2D\u5C08\u6848</p></div><div class="bg-white dark:bg-gray-800 rounded-lg-custom shadow-sm p-6"><h4 class="text-sm font-medium text-gray-600 dark:text-gray-400 mb-2">\u903E\u671F\u5C08\u6848</h4><p class="text-2xl font-bold text-red-600 dark:text-red-400">${ssrInterpolate(unref(dashboardData).overdue_projects || 0)}</p><p class="text-xs text-gray-500 dark:text-gray-400 mt-1">\u8D85\u904E\u9810\u671F\u5B8C\u6210\u65E5</p></div><div class="bg-white dark:bg-gray-800 rounded-lg-custom shadow-sm p-6"><h4 class="text-sm font-medium text-gray-600 dark:text-gray-400 mb-2">\u5373\u5C07\u5230\u671F</h4><p class="text-2xl font-bold text-yellow-600 dark:text-yellow-400">${ssrInterpolate(unref(dashboardData).upcoming_deadlines || 0)}</p><p class="text-xs text-gray-500 dark:text-gray-400 mt-1">7\u5929\u5167\u5230\u671F</p></div></div></div>`);
    };
  }
};
const _sfc_setup = _sfc_main.setup;
_sfc_main.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("pages/index.vue");
  return _sfc_setup ? _sfc_setup(props, ctx) : void 0;
};

export { _sfc_main as default };
//# sourceMappingURL=index-CLtl7oYz.mjs.map
