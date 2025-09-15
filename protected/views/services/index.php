<!-- Main Container -->
<div class="container mx-auto mt-10">
    <h1 class="text-2xl font-bold mb-4">Services</h1>

    <!-- Add Button -->
    <button onclick="openModal()" class="bg-blue-500 text-white px-4 py-2 rounded">Add Service</button>

    <!-- Add Service Modal -->
    <div id="modal" class="fixed z-10 inset-0 overflow-y-auto hidden">
        <div class="flex items-center justify-center min-h-screen">
            <div class="bg-white rounded shadow-lg p-6 w-1/3">
                <h2 class="text-xl font-semibold mb-4">Add Service</h2>
                <form id="service-form">
                    <label class="block mb-2">Name</label>
                    <input type="text" name="Services[name]" class="w-full border p-2 mb-4">

                    <label class="block mb-2">Description</label>
                    <textarea name="Services[description]" class="w-full border p-2 mb-4"></textarea>

                    <!-- Time Picker -->
                    <label class="block mb-2">Service Start Time</label>
                    <input type="time" name="Services[start_time]" class="w-full border p-2 mb-4">

                    <label class="block mb-2">Service End Time</label>
                    <input type="time" name="Services[end_time]" class="w-full border p-2 mb-4">

                    <label class="block mb-2">Duration (minutes)</label>
                    <input type="number" name="Services[duration]" class="w-full border p-2 mb-4" min="1">

                    <div class="flex justify-end">
                        <button type="button" onclick="closeModal()" class="bg-gray-300 px-4 py-2 mr-2">Cancel</button>
                        <button type="submit" class="bg-blue-500 text-white px-4 py-2">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Service Modal -->
    <div id="edit-modal" class="fixed z-10 inset-0 overflow-y-auto hidden">
        <div class="flex items-center justify-center min-h-screen">
            <div class="bg-white rounded shadow-lg p-6 w-1/3">
                <h2 class="text-xl font-semibold mb-4">Edit Service</h2>
                <form id="edit-service-form">
                    <input type="hidden" name="Services[id]" id="edit-id">

                    <label class="block mb-2">Name</label>
                    <input type="text" name="Services[name]" id="edit-name" class="w-full border p-2 mb-4">

                    <label class="block mb-2">Description</label>
                    <textarea name="Services[description]" id="edit-description" class="w-full border p-2 mb-4"></textarea>

                    <!-- Time Picker -->
                    <label class="block mb-2">Service Start Time</label>
                    <input type="time" name="Services[start_time]" id="edit-start_time" class="w-full border p-2 mb-4">

                    <label class="block mb-2">Service End Time</label>
                    <input type="time" name="Services[end_time]" id="edit-end_time" class="w-full border p-2 mb-4">

                    <label class="block mb-2">Duration (minutes)</label>
                    <input type="number" name="Services[duration]" id="edit-duration" class="w-full border p-2 mb-4" min="1">

                    <div class="flex justify-end">
                        <button type="button" onclick="closeEditModal()" class="bg-gray-300 px-4 py-2 mr-2">Cancel</button>
                        <button type="submit" class="bg-green-500 text-white px-4 py-2">Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Search -->
    <form id="search-form" class="mb-4 mt-4 flex gap-2">
        <input type="text" name="search" id="search-input" class="border px-4 py-2 rounded w-1/3"
            placeholder="Search services..."
            value="<?php echo CHtml::encode(isset($search) ? $search : ''); ?>">
        <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">Search</button>
    </form>

    <!-- Service Table -->
    <div id="service-table-container">
        <?php $this->renderPartial('_table', compact('services', 'pages')); ?>
    </div>
</div>

<!-- Toast Notification -->
<div id="toast" class="fixed top-5 right-5 z-50 hidden">
    <div id="toast-content" class="px-4 py-3 rounded shadow text-white"></div>
</div>

<!-- JavaScript -->
<script>

// Search
$('#search-form').submit(function(e) {
    e.preventDefault();
    const searchQuery = $('#search-input').val();
    $.ajax({
        url: '<?php echo $this->createUrl("services/index"); ?>',
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

// Pagination
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

function updateQueryStringParameter(uri, key, value) {
    var re = new RegExp("([?&])" + key + "=.*?(&|$)", "i");
    var separator = uri.indexOf('?') !== -1 ? "&" : "?";
    return uri.match(re)
        ? uri.replace(re, '$1' + key + "=" + encodeURIComponent(value) + '$2')
        : uri + separator + key + "=" + encodeURIComponent(value);
}

// Modals
function openModal() {
    document.getElementById("modal").classList.remove("hidden");
}
function closeModal() {
    document.getElementById("modal").classList.add("hidden");
}
function openEditModal() {
    document.getElementById("edit-modal").classList.remove("hidden");
}
function closeEditModal() {
    document.getElementById("edit-modal").classList.add("hidden");
}

// Toast
function showToast(message, type = 'success') {
    const toast = $('#toast');
    const toastContent = $('#toast-content');
    const bgColor = type === 'success' ? 'bg-green-500' : 'bg-red-500';

    toastContent.removeClass().addClass(`px-4 py-3 rounded shadow text-white ${bgColor}`).text(message);
    toast.removeClass('hidden');

    setTimeout(() => toast.addClass('hidden'), 3000);
}

// Add Service
$('#service-form').submit(function(e) {
    e.preventDefault();
    $.ajax({
        url: '<?php echo $this->createUrl("services/create"); ?>',
        type: 'POST',
        data: $(this).serialize(),
        success: function(response) {
            let res = JSON.parse(response);
            if (res.success) {
                $('#service-form')[0].reset();
                closeModal();
                showToast("Service added successfully!", "success");

                const service = res.service;
                const newRow = `
                    <tr data-id="${service.id}">
                        <td class="border px-4 py-2">${service.id}</td>
                        <td class="border px-4 py-2 service-name">${service.name}</td>
                        <td class="border px-4 py-2 service-description">${service.description}</td>
                        <td class="border px-4 py-2 flex gap-2">
                            <button onclick="editService(${service.id})" class="text-blue-500">Edit</button>
                            <button onclick="confirmDelete(${service.id}, this)" class="text-red-500">Delete</button>
                        </td>
                    </tr>
                `;
                $('table tbody').append(newRow);
            } else {
                alert("Failed to save service.");
            }
        }
    });
});

// Edit Service
function editService(id) {
    $.ajax({
        url: '<?php echo $this->createUrl("services/get"); ?>',
        type: 'GET',
        data: { id: id },
        success: function(response) {
            let data = JSON.parse(response);
            if (data.id) {
                $('#edit-id').val(data.id);
                $('#edit-name').val(data.name);
                $('#edit-description').val(data.description);
                $('#edit-start_time').val(data.start_time);
                $('#edit-end_time').val(data.end_time);
                $('#edit-duration').val(data.duration);
                openEditModal();
            } else {
                alert("Failed to fetch data.");
            }
        }
    });
}

// Update Service
$('#edit-service-form').submit(function(e) {
    e.preventDefault();

    $.ajax({
        url: '<?php echo $this->createUrl("services/update"); ?>',
        type: 'POST',
        data: $(this).serialize(),
        success: function(response) {
            let res = JSON.parse(response);
            if (res.success) {
                closeEditModal();
                showToast("Service updated successfully!", "success");

                const service = res.service;
                const row = $('tr[data-id="' + service.id + '"]');
                row.find('.service-name').text(service.name);
                row.find('.service-description').text(service.description);
            } else {
                alert("Failed to update service.");
            }
        }
    });
});

// Delete Service
function confirmDelete(id, button) {
    Swal.fire({
        title: 'Are you sure?',
        text: "You won't be able to undo this!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#e3342f',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Yes, delete it!',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: '<?php echo $this->createUrl("services/delete"); ?>',
                type: 'POST',
                data: { id: id },
                success: function () {
                    showToast("Service deleted successfully", "success");
                    $(button).closest('tr').remove();
                },
                error: function () {
                    showToast("Failed to delete service", "error");
                }
            });
        }
    });
}
</script>
