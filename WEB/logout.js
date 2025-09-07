const token = localStorage.getItem('access_token');
if (!token) {
    window.location.href = 'login.html'; 
 } 
//  else {
//       document.body.style.display = 'block'; // Hiển thị nội dung nếu đã đăng nhập
//   }
$(document).ready(function () {
    const user_name = localStorage.getItem("account_name") || "Tài khoản";
    const id_businesstype = localStorage.getItem("id_businesstype") || "3"

    $('#userNameDisplay').text(user_name);
    const role = localStorage.getItem('id_role');
    if (role === "1" ) {
        localStorage.clear();
      alert("Bạn không có quyền truy cập trang này!");
      window.location.href = 'login.html';
    }
    if(role ==="4")
    {
        $('#returnBtn').show();
    }
    if (id_businesstype ==="3"){
        $('#nav-order').show();
        $('#nav-product').show();
    }

    
  });
$.ajax({
    url: 'https://khaituanminh.atwebpages.com/api/my_profile.php',
    method: 'POST',
    contentType: 'application/json',
    data: JSON.stringify({ access_token: token }),
    success: function(res) {
        if (res.status === 'success') {
            localStorage.setItem('user_info', JSON.stringify(res.data));
            // localStorage.setItem('id_business', res.data.id_business); // nếu cần dùng riêng
            localStorage.setItem('id_role', res.data.id_role);
            localStorage.setItem('id_account', res.data.id_account);
         localStorage.setItem('id_user', res.data.id_user);
        } else {
            localStorage.clear();
            window.location.href = 'login.html';
        }
    },
    error: function() {
        localStorage.clear();
        window.location.href = 'login.html';
    }
});

$.ajax({
    url: 'https://khaituanminh.atwebpages.com/api/get_work.php',
    method: 'POST',
    contentType: 'application/json',
    data: JSON.stringify({ id_user : localStorage.getItem("id_user") 
}),
    success: function(res) {
        if (res.status === 'success') {
        } else {
            Swal.fire('Lỗi', res.message, 'error');
        }
    },
});
$.ajax({
    url: 'https://khaituanminh.atwebpages.com/api/get_apointment.php',
    method: 'POST',
    contentType: 'application/json',
    data: JSON.stringify({ id_user : localStorage.getItem("id_user")  }),
    success: function(res) {
        if (res.status === 'success') {
        } else {
            Swal.fire('Lỗi', res.message, 'error');
        }
    },
});   

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
                      window.location.href = 'login.html';
                  }
              });
          } else {
              if (confirm('Bạn có chắc muốn đăng xuất?')) {
                  localStorage.clear();
                  console.log('LocalStorage sau khi xóa:', localStorage);
                  window.location.href = 'login.html';
              }
          }
      });
  }
});

$(document).on('input', '.auto-resize', function () {
  this.style.height = 'auto';
  this.style.height = (this.scrollHeight) + 'px';
});




