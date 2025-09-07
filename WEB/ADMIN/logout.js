const token = localStorage.getItem('access_token');
if (!token) {
    window.location.href = '../login.html'; // Chuyển hướng ngay lập tức
 } 

 const role = localStorage.getItem('id_role');
 if (role < '4' ) { 
   alert("Bạn không có quyền truy cập trang này!");
   localStorage.clear();
   window.location.href = '../login.html';
 }

document.addEventListener('DOMContentLoaded', function () {
  const logoutBtn = document.getElementById('logoutBtn');
  if (logoutBtn) {
      logoutBtn.addEventListener('click', function () {
          if (typeof Swal !== 'undefined') {
              Swal.fire({
                  title: 'Đăng xuất?',
                  text: 'Bạn có chắc chắn muốn đăng xuất?',
                  icon: 'warning',
                  showCancelButton: true,
                  confirmButtonText: 'Đăng xuất',
                  cancelButtonText: 'Hủy'
              }).then((result) => {
                  if (result.isConfirmed) {
                      localStorage.clear();
                      console.log('LocalStorage sau khi xóa:', localStorage);
                      window.location.href = '../login.html';
                  }
              });
          } else {
              if (confirm('Bạn có chắc muốn đăng xuất?')) {
                  localStorage.clear();
                  console.log('LocalStorage sau khi xóa:', localStorage);
                  window.location.href = '../login.html';
              }
          }
      });
  }
});






