<div class="grid grid-cols-1 md:grid-cols-5 gap-6 mb-6">
    <!-- Appointments Today -->
    <div class="bg-white shadow rounded border-l-4 border-green-500 p-6">
        <h5 class="text-green-600 text-lg font-semibold mb-2">Today's Appointments</h5>
        <p id="today-count" class="text-5xl font-bold text-gray-900">0</p>
    </div>

    <!-- Appointments This Week -->
    <div class="bg-white shadow rounded border-l-4 border-yellow-500 p-6">
        <h5 class="text-yellow-600 text-lg font-semibold mb-2">This Week</h5>
        <p id="week-count" class="text-5xl font-bold text-gray-900">0</p>
    </div>

    <!-- Appointments This Month -->
    <div class="bg-white shadow rounded border-l-4 border-blue-500 p-6">
        <h5 class="text-blue-600 text-lg font-semibold mb-2">This Month</h5>
        <p id="month-count" class="text-5xl font-bold text-gray-900">0</p>
    </div>

    <!-- Completed Count -->
    <div class="bg-white shadow rounded border-l-4 border-blue-500 p-6">
        <h5 class="text-blue-600 text-lg font-semibold mb-2">Completed Count</h5>
        <p id="completed-count" class="text-5xl font-bold text-gray-900">0</p>
    </div>

    <!-- Cancelled Count -->
    <div class="bg-white shadow rounded border-l-4 border-blue-500 p-6">
        <h5 class="text-blue-600 text-lg font-semibold mb-2">Cancelled Count</h5>
        <p id="cancelled-count" class="text-5xl font-bold text-gray-900">0</p>
    </div>
</div>

   
<div id="calendar" class="w-full md:w-3/6 h-[600px] bg-white shadow-lg rounded-lg p-4"></div>

    
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        fetch('<?php echo Yii::app()->createUrl("site/appointmentStats"); ?>')
        .then(response => response.json())
        .then(data => {
            document.getElementById('today-count').textContent = data.today ?? 0;
            document.getElementById('week-count').textContent = data.week ?? 0;
            document.getElementById('month-count').textContent = data.month ?? 0;
            document.getElementById('completed-count').textContent = data.completed ?? 0;
            document.getElementById('cancelled-count').textContent = data.cancelled ?? 0;
        })
        .catch(error => console.error('Error:', error));
    });



document.addEventListener('DOMContentLoaded', function() {
    var calendarEl = document.getElementById('calendar');
    var calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        events: function(fetchInfo, successCallback, failureCallback) {
            // Make an AJAX request to get the appointments data from the backend
            $.ajax({
                url: '<?php echo Yii::app()->createUrl("appointment/getAppointments"); ?>',
                method: 'GET',
                dataType: 'json',
                success: function(data) {
                    // Pass the fetched data to FullCalendar
                    successCallback(data);
                },
                error: function(xhr, status, error) {
                    failureCallback(error);
                }
            });
        },
        eventRender: function(info) {
            // Optional: Customize event rendering (e.g., to include extra info, tooltips, etc.)
            info.el.title = info.event.extendedProps.description || ''; // Tooltip with description
        },
        eventColor: '#38b2ac',  // Example color for event
        eventTextColor: '#ffffff', // White text color for events
    });
    
    calendar.render();
});
    </script>
