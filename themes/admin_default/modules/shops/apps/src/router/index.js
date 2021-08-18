const routes = [
  {
    path: '',
    name: 'Index',
    component: () => import('../vue/views/Import.vue'),
    // children: [
    //   {
    //     path: '/:name',
    //     name: 'Invitee',
    //     component: () => import('../vue/views/invitation.vue'),
    //   },
    // ],
  },
]

export default routes
