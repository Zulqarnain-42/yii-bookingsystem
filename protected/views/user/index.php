



<div class="container mx-auto mt-10">
    <h1 class="text-2xl font-bold mb-4">Users</h1>
    <!-- Search Form -->
    <form id="search-form" class="mb-4 mt-4 flex gap-2">
        <input
            type="text"
            name="search"
            id="search-input"
            class="border px-4 py-2 rounded w-1/3"
            placeholder="Search Users..."
            autocomplete="off"
            value="<?php echo CHtml::encode(isset($search) ? $search : ''); ?>"
        >
        <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">Search</button>
    </form>

    <!-- User Table Container -->
    <div id="users-table-container">
        <?php $this->renderPartial('_table', compact('users', 'pages')); ?>
    </div>

</div>

<!-- Toast -->
<div id="toast" class="fixed top-5 right-5 z-50 hidden">
    <div id="toast-content" class="px-4 py-3 rounded shadow text-white"></div>
</div>

<script>

// 🔍 AJAX Search Submission
$('#search-form').submit(function(e) {
    e.preventDefault();
    const searchQuery = $('#search-input').val();

    $.ajax({
        url: '<?php echo $this->createUrl("user/index"); ?>',
        type: 'GET',
        data: { search: searchQuery },
        success: function(data) {
            $('#users-table-container').html(data);
        },
        error: function() {
            alert("Search failed.");
        }
    });
});


// Helper to inject query param
function updateQueryStringParameter(uri, key, value) {
    var re = new RegExp("([?&])" + key + "=.*?(&|$)", "i");
    var separator = uri.indexOf('?') !== -1 ? "&" : "?";
    if (uri.match(re)) {
        return uri.replace(re, '$1' + key + "=" + encodeURIComponent(value) + '$2');
    } else {
        return uri + separator + key + "=" + encodeURIComponent(value);
    }
}

// AJAX pagination handler
$(document).on('click', '.pagination a', function(e) {
    e.preventDefault();

    let url = $(this).attr('href');
    let searchQuery = $('#search-input').val();

    url = updateQueryStringParameter(url, 'search', searchQuery);

    $.ajax({
        url: url,
        type: 'GET',
        success: function(data) {
            $('#users-table-container').html(data);
        },
        error: function() {
            alert("Failed to load data.");
        }
    });
});

</script>
