@php
    $user = Auth::User();
    if ($user) {
        $unreadCount = $user->unreadNotifications->count();
        $notifications = $user->notifications->sortByDesc('created_at');;
    }
@endphp

@if($user)
<style>
    .preview-item.read { background-color: #e9ecef; }
    #notification-modal { color: #333; }
</style>
<a class="nav-link count-indicator dropdown-toggle d-flex align-items-center justify-content-center" id="notificationDropdown" href="#" data-toggle="dropdown">
    <i class="typcn typcn-bell mr-0"></i>
    <span class="count bg-danger">{{ $unreadCount }}</span>
</a>
<div class="dropdown-menu dropdown-menu-right navbar-dropdown preview-list" aria-labelledby="notificationDropdown">
    <p class="mb-0 font-weight-normal float-left dropdown-header">站內通知</p>
    @foreach($notifications as $notification)
        @php
            $header = $notification->header();
            $content = $notification->content();
            $readAt = $notification->read_at;
        @endphp

        <a
            class="dropdown-item preview-item{{ $readAt ? ' read' : '' }}" style="cursor: pointer;"
            data-header="{{ $header }}"
            data-content="{{ $content }}"
        >
            <div class="preview-thumbnail">
                <div class="preview-icon bg-warning">
                    <i class="typcn typcn-cog mx-0"></i>
                </div>
            </div>
            <div class="preview-item-content">
                <h6 class="preview-subject font-weight-normal">{{ $header }}</h6>
                <p class="font-weight-light small-text mb-0">
                    {{  Str::limit($content, 20) }}
                </p>
            </div>
        </a>
    @endforeach
</div>
<div class="modal" tabindex="-1" role="dialog" id="notification-modal">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="nm-header"></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p class="nm-content"></p>
            </div>
        </div>
    </div>
</div>

<script>
    (function () {
        const $countSpan = $('span.count');
        const $notificationModal = $('#notification-modal');
        const $modalHeader = $notificationModal.find('.nm-header');
        const $modalContent = $notificationModal.find('.nm-content');

        $('.dropdown-item').on('click', function (event) {
            event.stopPropagation();
            const header = $(this).data('header');
            const content = $(this).data('content');

            $modalHeader.text(header);
            $modalContent.text(content);

            $notificationModal.modal('show');
        });
    })();
</script>
@endif
