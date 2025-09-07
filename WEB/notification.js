const API_BASE   = 'https://khaituanminh.atwebpages.com/api';
const id_account = localStorage.getItem("id_account");

async function fetchNotifications() {
  try {
    const res = await $.ajax({
      url: `${API_BASE}/get_noti.php`,
      method: 'POST',
      contentType: 'application/json',
      data: JSON.stringify({ id_account })
    });
    if (res.status === 'success' && Array.isArray(res.data)) {
      return res.data
        .slice()
        .sort((a, b) => new Date(b.timecreated) - new Date(a.timecreated));
    } else {
      return [];
    }
  } catch (err) {
    console.error('Fetch notifications failed:', err);
    return [];
  }
}

function updateBadge(notifs) {
  const unreadCount = notifs.filter(n => !n.readstatus).length;
  const $badge = $('#notifBadge');
  if (unreadCount) {
    $badge.text(unreadCount).removeClass('d-none');
  } else {
    $badge.addClass('d-none');
  }
}

function renderDropdown(notifs) {
  const $menu = $('#notifDropdown').next('.dropdown-menu');

  if (!notifs.length) {
    return $menu.html(
      '<li class="text-center text-muted small">Không có thông báo</li>'
    );
  }

  const sortedNotifs = notifs
    .slice()
    .sort((a, b) => new Date(b.timecreated) - new Date(a.timecreated));

  const items = sortedNotifs
    .map(n => `
    <li class="notif-item dropdown-item p-2" 
        data-id="${n.id_noti}" data-expanded="false">
      <div class="d-flex justify-content-between align-items-center">
        <div class="notif-content text-truncate" style="
          max-width: 220px;
          white-space: nowrap;
          overflow: hidden;
          text-overflow: ellipsis;
        ">
          ${n.content}
        </div>
        <div class="d-flex align-items-center">
          ${
            n.readstatus
              ? ''
              : '<span class="badge bg-primary rounded-pill me-2">Mới</span>'
          }
          <button class="btn btn-sm btn-link notif-delete p-0" title="Xóa">
            <i class="bi bi-x-circle"></i>
          </button>
        </div>
      </div>
      <small class="text-muted mt-1">
        ${new Date(n.timecreated).toLocaleString('vi-VN')}
      </small>
    </li>
  `)
    .join('');

  $menu.html(items);
}

async function markAsRead(id_noti, $item) {
  try {
    await $.ajax({
      url: `${API_BASE}/check_noti.php`,
      method: 'POST',
      contentType: 'application/json',
      data: JSON.stringify({ id_noti })
    });
    $item.find('.badge').remove();
    decrementBadge();
  } catch (err) {
    console.error('Mark as read failed:', err);
  }
}

async function deleteNotification(id_noti, $item) {
  try {
    await $.ajax({
      url: `${API_BASE}/del_noti.php`,
      method: 'POST',
      contentType: 'application/json',
      data: JSON.stringify({ id_noti })
    });
    if ($item.find('.badge').length) decrementBadge();
    $item.remove();

    const $menu = $('#notifDropdown').next('.dropdown-menu');
    if ($menu.children().length === 0) {
      $menu.html(
        '<li class="text-center text-muted small">Không có thông báo</li>'
      );
    }
  } catch (err) {
    console.error('Delete notification failed:', err);
  }
}

function decrementBadge() {
  const $badge = $('#notifBadge');
  const current = parseInt($badge.text(), 10) || 0;
  if (current > 1) {
    $badge.text(current - 1);
  } else {
    $badge.addClass('d-none');
  }
}

$(function() {
  const dropdownToggleEl = document.querySelector('#notifDropdown');
  if (dropdownToggleEl) {
    if (bootstrap.Dropdown.getInstance(dropdownToggleEl)) {
      bootstrap.Dropdown.getInstance(dropdownToggleEl).dispose();
    }
    new bootstrap.Dropdown(dropdownToggleEl, { autoClose: 'outside' });
  }

  // lấy badge
  fetchNotifications().then(updateBadge);

  //  10s 
  setInterval(() => fetchNotifications().then(updateBadge), 10000);
  $('#notifDropdown').on('click', async () => {
    const notifs = await fetchNotifications();
    renderDropdown(notifs);
    updateBadge(notifs);
  });

  $(document).on('click', '.notif-item', function(e) {
    e.preventDefault();
    e.stopPropagation();

    if ($(e.target).closest('.notif-delete').length) {
      return;
    }

    const $item    = $(this);
    const $content = $item.find('.notif-content');
    const contentEl= $content.get(0);

    const isExpanded = $item.data('expanded') === true;
    const isTruncated = contentEl.offsetWidth < contentEl.scrollWidth;

    // Lần đầu nhấn 
    if (isTruncated && !isExpanded) {
      $content.css({
        'white-space': 'normal',
        'overflow': 'visible',
        'text-overflow': 'clip',
        'max-width': 'none'
      });
      $item.data('expanded', true);
      return;
    }

    // Lần nhấn thứ hai 
    if ($item.find('.badge').length) {
      markAsRead($item.data('id'), $item);
    }
  });

  $(document).on('click', '.notif-delete', function(e) {
    e.preventDefault();
    e.stopPropagation();
    const $item = $(this).closest('.notif-item');
    deleteNotification($item.data('id'), $item);
  });
});
