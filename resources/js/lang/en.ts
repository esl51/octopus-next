export default {
  global: {
    ok: 'Ok',
    cancel: 'Cancel',
    save: 'Save',
    add: 'Add',
    edit: 'Edit',
    delete: 'Delete',
    update: 'Update',
    new: 'New',
    search: 'Search...',
    show_password: 'Show/hide password',
    close: 'Close',
    confirm_title: 'Are you shure?',
    confirm_delete_body: 'If you proceed, "{name}" will be deleted.',
    yes_delete: 'Yes, delete',
    id_label: 'ID',
    name_label: 'Name',
    title_label: 'Заголовок',
    items_table_footer: 'Showing {from} to {to} of {total} entries',
    page_meta: '{from}-{to} of {total}',
    enable_light_mode: 'Enable light mode',
    enable_dark_mode: 'Enable dark mode',
  },
  error: {
    alert_title: 'Oops...',
    alert_text: 'Something went wrong. Try again later.',
    session_expired_title: 'Session expired',
    session_expired_text: 'Please log in again to continue',
    csrf_mismatch_title: 'CSRF token mismatch',
    csrf_mismatch_text: 'Page was refreshed',
    not_found_title: 'Oops… You just found an error page',
    not_found_text:
      'We are sorry but the page you are looking for was not found',
    home_button: 'Take me home',
  },
  auth: {
    login: {
      title: 'Login',
      headline: 'Login to your account',
      email_label: 'Email',
      email_placeholder: "your{'@'}email.com",
      password_label: 'Password',
      password_placeholder: 'Your password',
      forgot_password_link: 'I forgot password',
      remember_label: 'Remember me on this device',
      submit_button: 'Sign in',
      register_title: "Don't have account yet?",
      register_link: 'Sign up',
    },
    logout: {
      title: 'Logout',
    },
    register: {
      title: 'Register',
      headline: 'Create new account',
      name_label: 'Name',
      name_placeholder: 'Enter name',
      email_label: 'Email',
      email_placeholder: 'Enter email',
      password_label: 'Password',
      password_placeholder: 'Enter password',
      submit_button: 'Sign up',
      login_title: 'Already have account?',
      login_link: 'Sign in',
    },
    verification: {
      alert_title: 'Verify your email',
      alert_body:
        "Thanks for signing up! Before getting started, could you verify your email address by clicking on the link we just emailed to you? If you didn't receive the email, we will gladly send you another.",
      alert_send_button: 'Send verification',
      sent: 'A fresh verification link has been sent to your email address ({email}).',
      too_many_attempts_error: 'Too many attempts',
      verified: 'Your email address is verified.',
    },
    forgot_password: {
      title: 'Forgot password',
      text: 'Enter your email and we will send you a link to reset your password.',
      email_label: 'Email address',
      email_placeholder: 'Enter email',
      submit_button: 'Send',
      login_title: 'Forget it, I want to the sign in screen.',
      login_link: 'Send me back',
    },
    reset_password: {
      title: 'Reset password',
      text: 'Think of a new password and enter it below.',
      email_label: 'Email address',
      email_placeholder: "your{'@'}email.ru",
      password_label: 'Password',
      password_placeholder: 'Enter password',
      submit_button: 'Submit',
    },
    profile: {
      title: 'Profile',
      avatar_title: 'Avatar',
      change_avatar_button: 'Change avatar',
      delete_avatar_button: 'Delete avatar',
      delete_avatar_confirmation:
        'If you proceed, your avatar will be deleted.',
      details_title: 'Details',
      details_updated_text: 'Information updated',
      name_label: 'Name',
      name_placeholder: 'Enter name',
      email_label: 'Email',
      email_placeholder: 'Enter email',
      password_title: 'Change password',
      current_password_label: 'Current password',
      current_password_placeholder: 'Enter current password',
      password_label: 'New password',
      password_placeholder: 'Enter new password',
      password_updated_text: 'Password updated',
    },
  },
  layout: {
    open_user_menu: 'Open user menu',
  },
  dashboard: {
    title: 'Dashboard',
  },
}
