@if (session('success') || session('error'))
  <div 
    class="alert-container" 
    style="position: fixed; top: 20px; right: 20px; z-index: 9999; width: 300px;"
  >
    @if (session('success'))
      <div class="alert alert-success alert-dismissible fade in" role="alert" style="margin-bottom:10px;">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
        <strong>✔ Success!</strong> {{ session('success') }}
      </div>
    @endif

    @if (session('error'))
      <div class="alert alert-danger alert-dismissible fade in" role="alert" style="margin-bottom:10px;">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
        <strong>❌ Error!</strong> {{ session('error') }}
      </div>
    @endif
  </div>
@endif

<script>
  document.addEventListener('DOMContentLoaded', function() {
    // Auto-hide alerts after 4 seconds
    setTimeout(function() {
      var alerts = document.querySelectorAll('.alert');
      [].forEach.call(alerts, function(alert) {
        $(alert).fadeOut(500, function() {
          $(this).remove();
        });
      });
    }, 4000);
  });

  // Support dynamic toast event (optional)
  window.addEventListener('toast', function(e) {
    const { type, message } = e.detail;
    const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
    const icon = type === 'success' ? '✔' : '❌';

    const alertDiv = document.createElement('div');
    alertDiv.className = `alert ${alertClass} alert-dismissible fade in`;
    alertDiv.setAttribute('role', 'alert');
    alertDiv.innerHTML = `
      <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
      <strong>${icon}</strong> ${message}
    `;

    const container = document.querySelector('.alert-container') || (() => {
      const div = document.createElement('div');
      div.className = 'alert-container';
      div.style = 'position: fixed; top: 20px; right: 20px; z-index: 9999; width: 300px;';
      document.body.appendChild(div);
      return div;
    })();

    container.appendChild(alertDiv);

    setTimeout(() => $(alertDiv).fadeOut(500, () => alertDiv.remove()), 4000);
  });
</script>
