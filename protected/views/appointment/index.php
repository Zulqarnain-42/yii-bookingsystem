
<div class="container mx-auto mt-10">
    <h1 class="text-2xl font-bold mb-4">Appointments</h1>
    <!-- Search Form -->
    <form id="search-form" class="mb-4 mt-4 flex gap-2">
        <input
            type="text"
            name="search"
            id="search-input"
            class="border px-4 py-2 rounded w-1/3"
            placeholder="Search services..."
            value="<?php echo CHtml::encode(isset($search) ? $search : ''); ?>"
        >
        <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">Search</button>
    </form>

    <!-- Service Table Container -->
    <div id="service-table-container">
        <?php $this->renderPartial('_table', compact('appointments', 'pages')); ?>
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
        url: '<?php echo $this->createUrl("appointment/index"); ?>',
        type: 'GET',
        data: { search: searchQuery },
        success: function(data) {
            $('#service-table-container').html(data);
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
            $('#service-table-container').html(data);
        },
        error: function() {
            alert("Failed to load data.");
        }
    });
});


function showToast(message, type = 'success') {
    const toast = $('#toast');
    const toastContent = $('#toast-content');

    // Set styles
    let bgColor = type === 'success' ? 'bg-green-500' : 'bg-red-500';
    toastContent.removeClass().addClass(`px-4 py-3 rounded shadow text-white ${bgColor}`);
    toastContent.text(message);

    toast.removeClass('hidden');

    setTimeout(() => {
        toast.addClass('hidden');
    }, 3000); // Hide after 3 seconds
}

</script>
