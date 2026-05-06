@extends('admin.includes.layout')

@section('content')
<style>
@media (max-width: 768px) {
    .col-sm-6 {
        margin-bottom: 20px;
    }
}
</style>

<div class="content-wrapper">
  <section class="content-header">
    <div class="header-icon"><i class="fa fa-comments"></i></div>
    <div class="header-title">
      <h1>{{ __('messages.messages') }}</h1>
      <small>{{ __('messages.admin_chat') }}</small>
      <ol class="breadcrumb hidden-xs">
        <li><a href="{{ route('dashboard') }}"><i class="pe-7s-home"></i> {{ __('messages.home') }}</a></li>
        <li class="active">{{ __('messages.messages') }}</li>
      </ol>
    </div>
    <div class="header-action">
      <a href="{{ route('admin.chat.start_new') }}" class="btn btn-primary">
        <i class="fa fa-plus"></i> {{ __('messages.start_new_chat') }}
      </a>
    </div>
  </section>

  <section class="content">
    <!-- Summary Cards -->
  

    <div id="chat-list">
      @include('admin.chat._tables')
    </div>

  </section>
</div>

<script>
  (function() {
    function getUrl() {
      return "{{ route('admin.chat.index') }}" + "?ajax=1&_t=" + Date.now();
    }

    function withJquery() {
      var url = getUrl();
      if (!window.jQuery) { return false; }
      try {
        $.ajax({
          url: url,
          type: 'GET',
          cache: false,
          headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .done(function(html){
          var $container = $('#chat-list');
          if ($container.length) { $container.html(html); }
        })
        .fail(function(xhr){
          console.error('Chat list refresh (jQuery) failed', xhr.status, xhr.responseText);
        });
        return true;
      } catch (e) {
        console.error('jQuery refresh error', e);
        return false;
      }
    }

    function withFetch() {
      var url = getUrl();
      fetch(url, {
        headers: { 'X-Requested-With': 'XMLHttpRequest' },
        cache: 'no-store',
        credentials: 'same-origin'
      })
      .then(function(r){ return r.text(); })
      .then(function(html){
        var container = document.getElementById('chat-list');
        if (container) { container.innerHTML = html; }
      })
      .catch(function(err){ console.error('Chat list refresh (fetch) failed', err); });
    }

    function refreshChatList() {
      if (!withJquery()) { withFetch(); }
    }

    function start() {
      console.log('Starting chat list auto-refresh');
      refreshChatList();
      setInterval(refreshChatList, 5000);
    }

    if (document.readyState === 'loading') {
      document.addEventListener('DOMContentLoaded', start);
    } else {
      start();
    }
  })();
  </script>
@endsection
