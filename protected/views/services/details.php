<div class="max-w-6xl mx-auto p-6">
    <!-- Profile Section -->
    <div class="bg-gray-50 shadow-lg rounded-2xl overflow-hidden flex flex-col md:flex-row">
        <!-- Left: Image -->
        <div class="relative w-full md:w-1/3 h-64 md:h-auto">
            <img src="<?php echo Yii::app()->baseUrl . '/uploads/' . $model->image; ?>" 
                 alt="<?php echo CHtml::encode($model->name); ?>" 
                 class="w-full h-full object-cover">
        </div>

        <!-- Right: Info -->
        <div class="p-6 flex-1">
            <div class="flex justify-between items-start flex-wrap gap-4">
                <div>
                    <h2 class="text-2xl font-semibold text-gray-800"><?php echo CHtml::encode($model->name); ?></h2>
                </div>

                <div class="ml-auto">
                    <button 
                        onclick="checkLoginAndShowForm()" 
                        class="text-center bg-indigo-600 text-white font-semibold px-5 py-3 rounded-lg hover:bg-indigo-700 focus:outline-none focus:ring-4 focus:ring-indigo-300 shadow-lg transition duration-200">
                        Make an Appointment
                    </button>
                </div>
            </div>

            <!-- Appointment Form -->
            <div id="appointment-section" class="mt-6 hidden bg-white p-4 border rounded-lg shadow-sm">
                <?php echo CHtml::hiddenField('Services[id]', CHtml::encode($model->id)); ?>
                <label class="block mb-2 font-semibold text-gray-700">Select Date</label>
                <input type="date" id="appointment-datetime" class="w-full border p-2 rounded mb-4">

                <div id="available-slots" class="space-y-2 mb-4">
                    <!-- JS will populate this -->
                </div>

                <button class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">Confirm Appointment</button>
            </div>

            <!-- Contact Info -->
            <div class="mt-6 space-y-2 text-gray-700">
                <p><span class="font-semibold">Phone:</span></p>
                <p><span class="font-semibold">Email:</span></p>
                <p><span class="font-semibold">Address:</span></p>
            </div>
        </div>
    </div>

    <!-- Overview Section -->
    <div class="mt-10">
        <h3 class="text-xl font-bold text-gray-800 mb-4">Overview</h3>
        <p class="text-gray-700 leading-relaxed">
            <?php echo CHtml::encode($model->description); ?>
        </p>
    </div>
</div>





<script>
let selectedSlot = null;

function checkLoginAndShowForm() {
    fetch('<?php echo Yii::app()->createUrl("site/isLoggedIn"); ?>')
        .then(response => response.json())
        .then(data => {
            if (data.loggedIn) {
                showAppointmentForm();
            } else {
                window.location.href = "<?php echo Yii::app()->createUrl('site/login'); ?>";
            }
        })
        .catch(error => {
            console.error("Error checking login:", error);
        });
}

function showAppointmentForm() {
    const section = document.getElementById('appointment-section');
    section.classList.remove('hidden');
    showAvailableSlots();
}

function showAvailableSlots() {
    const slots = ['10:00 AM', '11:00 AM', '1:30 PM', '3:00 PM'];
    const container = document.getElementById('available-slots');
    container.innerHTML = '<label class="font-semibold text-gray-700 block mb-2">Available Slots:</label>';

    slots.forEach(slot => {
        const btn = document.createElement('button');
        btn.textContent = slot;
        btn.className = 'slot-btn bg-gray-200 hover:bg-gray-300 px-3 py-2 rounded mr-2 mt-2';
        btn.onclick = () => selectSlot(slot, btn);
        container.appendChild(btn);
    });
}

function selectSlot(slot, btn) {
    selectedSlot = slot;

    // Remove active styling from other buttons
    document.querySelectorAll('.slot-btn').forEach(b => b.classList.remove('bg-blue-500', 'text-white'));
    
    // Apply active styling
    btn.classList.add('bg-blue-500', 'text-white');
}

// Handle booking
document.querySelector('#appointment-section button.bg-green-600').addEventListener('click', function () {
    const date = document.getElementById('appointment-datetime').value;
    console.log('Selected Date:', date);
    console.log('Selected Slot:', selectedSlot);
    const serviceId = document.querySelector('input[name="Services[id]"]').value;
    console.log('Service ID:', serviceId);
    if (!date || !selectedSlot) {
        showToast('Please select both date and time slot.', 'error');
        return;
    }

    fetch('<?php echo Yii::app()->createUrl("appointment/book"); ?>', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify({
            service_id: serviceId,
            date: date,
            time: selectedSlot
        })
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            showToast('Appointment booked successfully!', 'success');
            document.getElementById('appointment-section').classList.add('hidden');
        } else {
            showToast(data.message || 'Failed to book appointment.', 'error');
        }
    })
    .catch(err => {
        console.error(err);
        showToast('An error occurred. Please try again.', 'error');
    });
});

function showToast(message, type = 'info') {
    const toast = document.getElementById('toast');
    toast.textContent = message;

    // Apply styling based on type
    toast.className = `fixed bottom-6 right-6 px-4 py-3 rounded shadow-lg z-50 transition-opacity duration-300 ${
        type === 'success' ? 'bg-green-600' :
        type === 'error' ? 'bg-red-600' : 'bg-gray-800'
    }`;

    toast.classList.remove('hidden');
    toast.style.opacity = '1';

    // 🔁 Show for 6 seconds, then fade out
    setTimeout(() => {
        toast.style.opacity = '0';
        setTimeout(() => toast.classList.add('hidden'), 300); // Wait for fade-out animation
    }, 6000); // 🔄 Toast stays for 6 seconds
}

</script>
