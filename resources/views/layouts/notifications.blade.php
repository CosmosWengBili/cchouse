@php
    $user = Auth::User();
    if ($user) {
        $unreadCount = $user->unreadNotifications->count();
        $notifications = $user->notifications->sortByDesc('created_at');;
    }
@endphp

@if($user)
<style>
    .preview-list {
        max-height: 300px;
        overflow-y: auto;
    }
    .preview-item.read { background-color: #e9ecef; }
    .sent-at {
        text-align: right;
        font-size: 12px;
        color: 666;
        border-top: 1px solid #aaa;
        margin-top: 8px;
        padding-top: 4px;
    }
    #notification-modal { color: #333; }
</style>
<a class="nav-link count-indicator dropdown-toggle d-flex align-items-center justify-content-center" id="notificationDropdown" href="#" data-toggle="dropdown">
    <i class="typcn typcn-bell mr-0"></i>
    <span class="count bg-danger" style="{{ $unreadCount == 0 ? 'display: none;' : '' }}">{{ $unreadCount }}</span>
</a>
<div class="dropdown-menu dropdown-menu-right navbar-dropdown preview-list" aria-labelledby="notificationDropdown">
    <p class="mb-0 font-weight-normal float-left dropdown-header">站內通知</p>
    @foreach($notifications as $notification)
        @php
            $id = $notification->id;
            $header = $notification->header();
            $content = $notification->content();
            $readAt = $notification->read_at;
            $createdAt = $notification->created_at;
        @endphp

        <a
            class="dropdown-item preview-item{{ $readAt ? ' read' : '' }}" style="cursor: pointer;"
            data-id="{{ $id }}"
            data-read-at="{{ $readAt }}"
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

                    <div class="sent-at">{{ $createdAt }}</div>
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
            const $notification = $(this);
            const header = $notification.data('header');
            const content = $notification.data('content');
            const isRead = $notification.data('read-at').length > 0;
            const id = $notification.data('id');

            $modalHeader.text(header);
            $modalContent.html(content);

            $notificationModal.modal('show');

            if (!isRead) {
                $.post('notifications/' + id, {} , function () {
                    const count = Number($countSpan.text());
                    const newCount = count - 1;
                    if (newCount > 0) {
                        $countSpan.text(newCount);
                    } else {
                        $countSpan.hide();
                    }

                    $notification.addClass('read').data('read-at', new Date());
                })
            }
        });
    })();
</script>
@endif
