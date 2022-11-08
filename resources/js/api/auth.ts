import api from './'

export const authUrls = {
  user: '/api/auth/user',
  csrfCookie: '/sanctum/csrf-cookie',
  login: '/login',
  logout: '/logout',
  forgotPassword: '/forgot-password',
  resetPassword: '/reset-password',
  register: '/register',
  verify: '/email/verify',
  verificationNotification: '/email/verification-notification',
  updateAvatar: '/api/auth/avatar',
  deleteAvatar: '/api/auth/avatar',
  profileInformation: '/user/profile-information',
  password: '/user/password',
}

export const authApi = {
  // user
  async user() {
    const { data } = await api.get(authUrls.user)
    return data
  },

  // login
  async csrfCookie() {
    await api.get(authUrls.csrfCookie)
  },
  async login(payload: { email: string; password: string }) {
    await api.post(authUrls.login, payload)
  },
  async logout() {
    await api.post(authUrls.logout)
  },

  // password reset
  async forgotPassword(payload: { email: string }) {
    await api.post(authUrls.forgotPassword, payload)
  },
  async resetPassword(payload: {
    token: string
    email: string
    password: string
  }) {
    await api.post(authUrls.resetPassword, payload)
  },

  // register
  async register(payload: { name: string; email: string; password: string }) {
    await api.post(authUrls.register, payload)
  },

  // email verification
  async verify(payload: { id: string; hash: string }) {
    await api.post(authUrls.verify, payload)
  },
  async verificationNotification() {
    await api.post(authUrls.verificationNotification)
  },

  // update avatar
  async updateAvatar(payload: { avatar: File }) {
    const formData = payload as { avatar: File; _method: string }
    formData._method = 'PUT'
    await api.post(authUrls.updateAvatar, formData, {
      headers: {
        'Content-Type': 'multipart/form-data',
      },
    })
  },

  // delete avatar
  async deleteAvatar() {
    await api.delete(authUrls.deleteAvatar)
  },

  // profile information
  async profileInformation(payload: { name: string; email: string }) {
    await api.put(authUrls.profileInformation, payload)
  },

  // password
  async password(payload: { current_password: string; password: string }) {
    await api.put(authUrls.password, payload)
  },
}

export default authApi
