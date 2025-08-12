<template>
  <div 
    class="relative"
    @mouseenter="showTooltip = true"
    @mouseleave="showTooltip = false"
  >
    <button
      @click="toggleItem"
      class="w-full flex items-center px-3 py-2 text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-all duration-200 group"
      :class="{ 
        'justify-center': collapsed,
        'bg-primary-50 dark:bg-primary-900/20 text-primary-600 dark:text-primary-400': isCurrentRoute
      }"
    >
      <!-- Icon -->
      <component 
        :is="getIcon(item.icon)" 
        class="w-5 h-5 transition-colors duration-200" 
        :class="isCurrentRoute ? 'text-primary-600 dark:text-primary-400' : 'text-gray-500 group-hover:text-primary-500'"
      />
      
      <!-- Text and Arrow (desktop) -->
      <transition
        enter-active-class="transition-all duration-300 delay-150"
        enter-from-class="opacity-0 transform -translate-x-2"
        enter-to-class="opacity-100 transform translate-x-0"
        leave-active-class="transition-all duration-150"
        leave-from-class="opacity-100 transform translate-x-0"
        leave-to-class="opacity-0 transform -translate-x-2"
      >
        <div v-if="!collapsed" class="flex items-center justify-between flex-1 ml-3 overflow-hidden">
          <span class="font-medium whitespace-nowrap">{{ item.name }}</span>
          <ChevronDownIcon 
            v-if="item.children"
            class="w-4 h-4 transition-transform duration-300 flex-shrink-0"
            :class="{ 'rotate-180': isExpanded }"
          />
        </div>
      </transition>
    </button>

    <!-- Tooltip for collapsed state -->
    <transition
      enter-active-class="transition-all duration-200 ease-out"
      enter-from-class="opacity-0 transform scale-90 translate-x-1"
      enter-to-class="opacity-100 transform scale-100 translate-x-0"
      leave-active-class="transition-all duration-150 ease-in"
      leave-from-class="opacity-100 transform scale-100 translate-x-0"
      leave-to-class="opacity-0 transform scale-90 translate-x-1"
    >
      <div
        v-if="collapsed && showTooltip"
        class="absolute left-full top-1/2 ml-3 px-3 py-2 bg-gray-900 dark:bg-gray-700 text-white text-sm rounded-lg pointer-events-none z-50 whitespace-nowrap shadow-lg"
        style="transform: translateY(-50%)"
      >
        {{ item.name }}
        <!-- Arrow -->
        <div class="absolute right-full top-1/2 w-0 h-0 border-t-4 border-b-4 border-r-4 border-transparent border-r-gray-900 dark:border-r-gray-700" style="transform: translateY(-50%)"></div>
      </div>
    </transition>

    <!-- Submenu -->
    <transition
      enter-active-class="transition-all duration-200 ease-out"
      enter-from-class="opacity-0 max-h-0"
      enter-to-class="opacity-100 max-h-96"
      leave-active-class="transition-all duration-200 ease-in"
      leave-from-class="opacity-100 max-h-96"
      leave-to-class="opacity-0 max-h-0"
    >
      <div v-if="isExpanded && !collapsed && item.children" class="ml-8 mt-2 space-y-1 overflow-hidden">
        <NuxtLink
          v-for="child in item.children"
          :key="child.name"
          :to="child.href"
          class="block px-3 py-2 text-sm text-gray-500 dark:text-gray-400 hover:text-primary-500 hover:bg-gray-50 dark:hover:bg-gray-700 rounded-lg transition-all duration-200"
        >
          {{ child.name }}
        </NuxtLink>
      </div>
    </transition>
  </div>
</template>

<script setup>
import { 
  ChartBarIcon,
  CogIcon,
  QuestionMarkCircleIcon,
  ChevronDownIcon,
  FolderIcon,
  UsersIcon,
  UserGroupIcon
} from '@heroicons/vue/24/outline'

const props = defineProps({
  item: {
    type: Object,
    required: true
  },
  collapsed: {
    type: Boolean,
    default: false
  }
})

const route = useRoute()
const isExpanded = ref(false)
const showTooltip = ref(false)

// Check if this item or any of its children is the current route
const isCurrentRoute = computed(() => {
  if (props.item.href === route.path) {
    return true
  }
  if (props.item.children) {
    return props.item.children.some(child => child.href === route.path)
  }
  return false
})

// Auto-expand if current route is a child
watch(() => route.path, (newPath) => {
  if (props.item.children) {
    const hasActiveChild = props.item.children.some(child => child.href === newPath)
    if (hasActiveChild) {
      isExpanded.value = true
    }
  }
}, { immediate: true })

const toggleItem = () => {
  if (props.item.children && !props.collapsed) {
    isExpanded.value = !isExpanded.value
  } else if (props.item.children && props.collapsed) {
    // For collapsed state, we could show a popover menu here in the future
  } else if (props.item.href) {
    // Navigate to the href if the item has one and no children
    navigateTo(props.item.href)
  }
}

const iconComponents = {
  ChartBarIcon,
  CogIcon,
  QuestionMarkCircleIcon,
  FolderIcon,
  UsersIcon,
  UserGroupIcon
}

const getIcon = (iconName) => {
  return iconComponents[iconName] || ChartBarIcon
}
</script>