

function showDeleteDetail(post) {
    $("#post-delete #post-id").text(post.id);
    $("#post-delete #post-title").text(post.title);
    $("#post-delete #post-description").text(post.description);
    $("#deleteId").val(post.id);
    if (post.status == "0") {
        $("#post-delete #post-status").text("Inactive");
    } else {
        $("#post-delete #post-status").text("Active");
    }
}

function showPostDetail(post) {
    $("#post-detail #post-title").text(post.title);
    $("#post-detail #post-description").text(post.description);
    if (post.status == "0") {
        $("#post-detail #post-status").text("Inactive");
    } else {
        $("#post-detail #post-status").text("Active");
    }
    $("#post-detail #post-created-at").text(
        moment(post.created_at).format("YYYY/MM/DD")
    );
    $("#post-detail #post-created-user").text(post.created_user);
    $("#post-detail #post-updated-at").text(
        moment(post.updated_at).format("YYYY/MM/DD")
    );
    $("#post-detail #post-updated-user").text(post.updated_user);
}


