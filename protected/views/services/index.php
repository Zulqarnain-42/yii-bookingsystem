



<div class="container mx-auto mt-10">
    <h1 class="text-2xl font-bold mb-4">Services</h1>

    <!-- Button to open modal -->
    <button onclick="openModal()" class="bg-blue-500 text-white px-4 py-2 rounded">Add Service</button>

    <!-- Modal -->
    <div id="modal" class="fixed z-10 inset-0 overflow-y-auto hidden">
        <div class="flex items-center justify-center min-h-screen">
            <div class="bg-white rounded shadow-lg p-6 w-1/3">
                <h2 class="text-xl font-semibold mb-4">Add Service</h2>
                <form id="service-form" enctype="multipart/form-data">
                    <label class="block mb-2">Name</label>
                    <input type="text" name="Services[name]" class="w-full border p-2 mb-4">

                    <label class="block mb-2">Description</label>
                    <textarea name="Services[description]" class="w-full border p-2 mb-4"></textarea>

                    <label class="block mb-2">Upload Picture</label>
                    <input type="file" name="Services[image]" accept="image/*" class="w-full border p-2 mb-4">

                    <!-- Availability section -->
                    <label class="block mb-2">Availability</label>
                    <hr/>
                    <div class="flex mb-4 gap-2">
                        <div class="flex-1">
                            <label class="block text-sm mb-1">Start Time</label>
                            <input id="start-time" type="text" name="Services[start_time]" class="w-full border p-2">
                        </div>
                        <div class="flex-1">
                            <label class="block text-sm mb-1">End Time</label>
                            <input id="end-time" type="text" name="Services[end_time]" class="w-full border p-2">
                        </div>
                    </div>

                    <div class="flex justify-end">
                        <button type="button" onclick="closeModal()" class="bg-gray-300 px-4 py-2 mr-2">Cancel</button>
                        <button type="submit" class="bg-blue-500 text-white px-4 py-2">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>


    <!-- Edit Modal -->
    <div id="edit-modal" class="fixed z-10 inset-0 overflow-y-auto hidden">
        <div class="flex items-center justify-center min-h-screen">
            <div class="bg-white rounded shadow-lg p-6 w-1/3">
                <h2 class="text-xl font-semibold mb-4">Edit Service</h2>
                <form id="edit-service-form" enctype="multipart/form-data">
                    <input type="hidden" name="Services[id]" id="edit-id">

                    <label class="block mb-2">Name</label>
                    <input type="text" name="Services[name]" id="edit-name" class="w-full border p-2 mb-4">

                    <label class="block mb-2">Description</label>
                    <textarea name="Services[description]" id="edit-description" class="w-full border p-2 mb-4"></textarea>

                    <!-- Image Preview (hidden if no image) -->
                    <div id="edit-image-preview" class="mb-4">
                        <label class="block mb-2">Current Picture</label>
                        <img id="edit-image" src="" alt="Current Image" class="w-32 h-32 object-cover border" />
                    </div>


                    <label class="block mb-2">Upload Picture</label>
                    <input type="file" name="Services[image]" accept="image/*" class="w-full border p-2 mb-4">

                    <!-- Availability section -->
                    <label class="block mb-2">Availability</label>
                    <hr/>
                    <div class="flex mb-4 gap-2">
                        <div class="flex-1">
                            <label class="block text-sm mb-1">Start Time</label>
                            <input id="edit-start-time" type="text" name="Services[start_time]" class="w-full border p-2">
                        </div>
                        <div class="flex-1">
                            <label class="block text-sm mb-1">End Time</label>
                            <input id="edit-end-time" type="text" name="Services[end_time]" class="w-full border p-2">
                        </div>
                    </div>

                    <div class="flex justify-end">
                        <button type="button" onclick="closeEditModal()" class="bg-gray-300 px-4 py-2 mr-2">Cancel</button>
                        <button type="submit" class="bg-green-500 text-white px-4 py-2">Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
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
        <?php $this->renderPartial('_table', compact('services', 'pages')); ?>
    </div>

</div>

<!-- Toast -->
<div id="toast" class="fixed top-5 right-5 z-50 hidden">
    <div id="toast-content" class="px-4 py-3 rounded shadow text-white"></div>
</div>

<script>

    flatpickr("#start-time", {
        enableTime: true,
        noCalendar: true,
        dateFormat: "H:i",
        time_24hr: true
    });

    flatpickr("#end-time", {
        enableTime: true,
        noCalendar: true,
        dateFormat: "H:i",
        time_24hr: true
    });

// 🔍 AJAX Search Submission
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


function openModal() {
    document.getElementById("modal").classList.remove("hidden");
}
function closeModal() {
    document.getElementById("modal").classList.add("hidden");
}

// Handle AJAX form submission
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

                // Append new row to table
                const service = res.service;
                const newRow = `
                    <tr>
                        <td class="border px-4 py-2">${service.id}</td>
                        <td class="border px-4 py-2">${service.name}</td>
                        <td class="border px-4 py-2">${service.description}</td>
                        <td class="border px-4 py-2 flex gap-2">
                            <button onclick="editService(${service.id})" class="text-blue-500">Edit</button>
                            <a href="<?php echo $this->createUrl('services/delete'); ?>?id=${service.id}" class="text-red-500" onclick="return confirm('Delete this service?')">Delete</a>
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

function confirmDelete(id, button) {
    Swal.fire({
        title: 'Are you sure?',
        text: "You won't be able to undo this!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#e3342f', // red
        cancelButtonColor: '#6c757d', // gray
        confirmButtonText: 'Yes, delete it!',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            // Set CSRF token for all AJAX POST requests
            $.ajaxSetup({
                headers: {
                    'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $.ajax({
                url: '<?php echo $this->createUrl("services/delete"); ?>',
                type: 'POST',
                data: { id: id },
                success: function () {
                    showToast("Service deleted successfully", "success");

                    // Remove row from table
                    $(button).closest('tr').remove();
                },
                error: function () {
                    showToast("Failed to delete service", "error");
                }
            });
        }
    });
}


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
                $('#edit-start-time').val(data.start_time);
                $('#edit-end-time').val(data.end_time);
                // Show image if available
                if (data.image) {
                    const imagePath = '/uploads/' + data.image; // Adjust path if needed
                    document.getElementById('edit-image').src = imagePath;
                    document.getElementById('edit-image-preview').style.display = 'block';
                } else {
                    document.getElementById('edit-image-preview').style.display = 'none';
                }
                openEditModal();
            } else {
                alert("Failed to fetch data.");
            }
        }
    });
}

function openEditModal() {
    document.getElementById("edit-modal").classList.remove("hidden");
}

function closeEditModal() {
    document.getElementById("edit-modal").classList.add("hidden");
}

// Handle edit form submission
$('#edit-service-form').submit(function(e) {
    e.preventDefault();

    const id = $('#edit-id').val();

    let formData = $(this).serializeArray();
    formData.push({ name: 'id', value: id }); // ✅ include id in POST body

    $.ajax({
        url: '<?php echo $this->createUrl("services/update"); ?>',
        type: 'POST',
        data: formData,
        success: function(response) {
            let res = JSON.parse(response);
            if (res.success) {
                closeEditModal(); // Optional
                showToast("Service updated successfully!", "success");
                // Or reload the page:
                // location.reload();
                const service = res.service;

                // ✅ Update the table row
                const row = $('tr[data-id="' + service.id + '"]');
                row.find('.service-name').text(service.name);
                row.find('.service-description').text(service.description);
            } else {
                alert("Failed to update service.");
            }
        }
    });
});


</script>
