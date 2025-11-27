/* Small SweetAlert2 helper for agent pages
   - Provides a simple toast and confirm wrapper without touching other code
   - Safe to include on pages that already use Swal
*/
(function(){
  function toast(message, icon='success', timer=2500){
    if (window.Swal && typeof Swal.fire === 'function'){
      Swal.fire({
        toast: true,
        position: 'top-end',
        icon: icon,
        title: message,
        showConfirmButton: false,
        timer: timer,
        timerProgressBar: true
      });
    } else {
      console.log('[Toast]', icon, message);
    }
  }

  function confirmAction(opts){
    // opts: { title, text, confirmButtonText, cancelButtonText, icon }
    if (window.Swal && typeof Swal.fire === 'function'){
      return Swal.fire({
        title: opts.title || 'Confirm',
        text: opts.text || '',
        icon: opts.icon || 'warning',
        showCancelButton: true,
        confirmButtonText: opts.confirmButtonText || 'Yes',
        cancelButtonText: opts.cancelButtonText || 'Cancel',
        reverseButtons: true
      });
    }
    // Fallback: resolve as cancelled
    return Promise.resolve({ isConfirmed: false });
  }

  window.AgentSwal = { toast, confirmAction };
})();
