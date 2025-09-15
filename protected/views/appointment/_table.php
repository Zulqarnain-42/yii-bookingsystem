<table class="table-auto w-full text-left border-collapse">
    <thead>
        <tr class="bg-gray-100 text-gray-700">
            <th class="p-3">Username</th>
            <th class="p-3">Service Name</th>
            <th class="p-3">Date</th>
            <th class="p-3">Time</th>
            <th class="p-3">Status</th>
            <th class="p-3">Appointment Status</th>
            <th class="p-3">Actions</th> <!-- New column -->
        </tr>
    </thead>
    <tbody>
        <?php foreach ($appointments as $appointment): ?>
            <tr class="border-b" data-id="<?php echo $appointment->id; ?>">
                <td class="p-3"><?php echo CHtml::encode($appointment->user->username); ?></td>
                <td class="p-3"><?php echo CHtml::encode($appointment->service->name); ?></td>
                <td class="p-3 service-date"><?php echo CHtml::encode($appointment->appointment_date); ?></td>
                <td class="p-3 service-time"><?php echo CHtml::encode($appointment->appointment_time); ?></td>
                <td class="p-3 service-status"><?php echo CHtml::encode(ucfirst($appointment->status)); ?></td>
                <td class="p-3 service-status"><?php echo CHtml::encode(ucfirst($appointment->appointment_status)); ?></td>
                    <td class="p-3">
                        <?php if (strtolower($appointment->appointment_status) !== 'cancelled' && strtolower($appointment->appointment_status) !== 'completed'): ?>
                            <?php if (Yii::app()->user->role !== 'staff' || Yii::app()->user->role === 'admin'): ?>
                                <button class="complete-btn bg-blue-500 text-white px-3 py-1 rounded" data-id="<?php echo $appointment->id; ?>">Complete</button>
                            <?php endif; ?>
                            <button class="cancel-btn bg-red-600 text-white px-3 py-1 rounded" data-id="<?php echo $appointment->id; ?>">Cancel</button>
                        <?php endif; ?>

                    </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<!-- Pagination -->
<div class="mt-4">
    <?php
    $this->widget('CLinkPager', [
        'pages' => $pages,
        'header' => '',
        'htmlOptions' => ['class' => 'pagination flex space-x-2'],
        'selectedPageCssClass' => 'bg-blue-500 text-white',
    ]);
    ?>
</div>


<!-- Toast Notification -->
<div id="toast" class="fixed bottom-6 right-6 px-4 py-3 rounded shadow-lg text-white hidden z-50 transition-opacity duration-300"></div>


<script>

function showToast(message, type = 'success') {
    const toast = document.getElementById('toast');
    toast.textContent = message;

    const bgColor = type === 'success' ? 'bg-green-600' : type === 'error' ? 'bg-red-600' : 'bg-gray-800';
    toast.className = `fixed bottom-6 right-6 px-4 py-3 rounded shadow-lg text-white z-50 transition-opacity duration-300 ${bgColor}`;
    
    toast.classList.remove('hidden');
    toast.style.opacity = '1';

    setTimeout(() => {
        toast.style.opacity = '0';
        setTimeout(() => toast.classList.add('hidden'), 300);
    }, 4000); // 4 seconds
}

document.querySelectorAll('.cancel-btn').forEach(button => {
    button.addEventListener('click', () => {
        const id = button.dataset.id;
        if (!confirm('Are you sure you want to cancel this appointment?')) return;

        fetch('<?php echo Yii::app()->createUrl("appointment/cancel"); ?>', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({ id: id })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                const row = document.querySelector(`tr[data-id="${id}"]`);
                row.querySelector('.service-status').textContent = 'Cancelled';
                // Remove cancel button, show label instead
                button.remove();
                const actionCell = row.querySelector('td:last-child');
                const cancelledLabel = document.createElement('span');
                cancelledLabel.className = 'text-gray-500 italic';
                cancelledLabel.textContent = 'Cancelled';
                actionCell.appendChild(cancelledLabel);
                showToast('Appointment cancelled successfully.');
            } else {
                showToast(data.message || 'Failed to cancel appointment.', 'error');
            }
        })
        .catch(() => showToast('An error occurred while cancelling appointment.', 'error'));
    });
});

document.querySelectorAll('.complete-btn').forEach(button => {
    button.addEventListener('click', () => {
        const id = button.dataset.id;

        // Confirm the action
        if (!confirm('Are you sure you want to mark this appointment as completed?')) return;

        // Send AJAX request to update appointment status
        fetch('<?php echo Yii::app()->createUrl("appointment/complete"); ?>', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({ id: id })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                const row = document.querySelector(`tr[data-id="${id}"]`);
                
                // Update the status in the row
                row.querySelector('.service-status').textContent = 'Completed';
                
                // Remove the Complete button
                button.remove();

                // Optionally, add a "Completed" label or modify action buttons
                const actionCell = row.querySelector('td:last-child');
                const completedLabel = document.createElement('span');
                completedLabel.className = 'text-gray-500 italic';
                completedLabel.textContent = 'Completed';
                actionCell.appendChild(completedLabel);

                showToast('Appointment marked as completed successfully.');
            } else {
                showToast(data.message || 'Failed to mark appointment as completed.', 'error');
            }
        })
        .catch(() => showToast('An error occurred while marking the appointment as completed.', 'error'));
    });
});




</script>
