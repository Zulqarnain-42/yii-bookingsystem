<table class="table-auto w-full text-left border-collapse">
    <thead>
        <tr class="bg-gray-100 text-gray-700">
            <th class="p-3">Username</th>
            <th class="p-3">Service Name</th>
            <th class="p-3">Date</th>
            <th class="p-3">Time</th>
            <th class="p-3">Status</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($appointments as $appointment): ?>
            <tr class="border-b">
                <td class="p-3"><?php echo CHtml::encode($appointment->user->username); ?></td>
                <td class="p-3"><?php echo CHtml::encode($appointment->service->name); ?></td>
                <td class="p-3"><?php echo CHtml::encode($appointment->appointment_date); ?></td>
                <td class="p-3"><?php echo CHtml::encode($appointment->appointment_time); ?></td>
                <td class="p-3"><?php echo CHtml::encode(ucfirst($appointment->status)); ?></td>
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
