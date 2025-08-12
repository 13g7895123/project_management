export const useSettingsStore = defineStore('settings', () => {
  const showFootbar = ref(true)
  const sidebarMenuItems = ref([
    {
      name: '儀表板',
      icon: 'ChartBarIcon',
      href: '/'
    },
    {
      name: '專案管理',
      icon: 'FolderIcon',
      children: [
        { name: '專案列表', href: '/projects' },
        { name: '新增專案', href: '/projects/create' }
      ]
    },
    {
      name: '業主管理',
      icon: 'UsersIcon',
      children: [
        { name: '業主列表', href: '/clients' },
        { name: '新增業主', href: '/clients/create' }
      ]
    },
    {
      name: '用戶管理',
      icon: 'UserGroupIcon',
      children: [
        { name: '用戶列表', href: '/settings/users' }
      ]
    },
    {
      name: '設定',
      icon: 'CogIcon',
      children: [
        { name: '主題設定', href: '/settings/theme' },
        { name: '一般設定', href: '/settings' }
      ]
    }
  ])
  
  const toggleFootbar = () => {
    showFootbar.value = !showFootbar.value
  }
  
  const updateMenuItems = (newItems) => {
    sidebarMenuItems.value = newItems
  }
  
  return {
    showFootbar,
    sidebarMenuItems,
    toggleFootbar,
    updateMenuItems
  }
})