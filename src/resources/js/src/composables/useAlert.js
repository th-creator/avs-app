import Swal from "sweetalert2";

export const useAlert = (type, message, options = { timer: 3000 }) => {
        Swal.mixin({
          toast: true,
          position: 'bottom-start',
          showConfirmButton: false,
          timer: options.timer,
          showCloseButton: true,
          customClass: {
            popup: `color-${type}`
          },
          target: 'body'
        }).fire({
          title: message,
        });
}
