<table class="table-auto w-full border-collapse border border-gray-300">
    <thead>
        <tr class="bg-gray-100">
            <th class="border px-4 py-2">ID</th>
            <th class="border px-4 py-2">Name</th>
            <th class="border px-4 py-2">Description</th>
            <th class="border px-4 py-2">Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($services as $service): ?>
            <tr data-id="<?php echo $service->id; ?>">
                <td class="border px-4 py-2"><?php echo $service->id; ?></td>
                <td class="border px-4 py-2 service-name"><?php echo CHtml::encode($service->name); ?></td>
                <td class="border px-4 py-2 service-description"><?php echo CHtml::encode($service->description); ?></td>
                <td class="border px-4 py-2 flex gap-2">
                    <button onclick="editService(<?php echo $service->id; ?>)" class="edit-btn bg-blue-500 text-white px-3 py-1 rounded">Edit</button>
                    <button onclick="confirmDelete(<?php echo $service->id; ?>, this)" class="cancel-btn bg-red-600 text-white px-3 py-1 rounded">Delete</button>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<!-- Pagination -->
<div class="pagination mt-4">
    <?php
    $this->widget('CLinkPager', array(
        'pages' => $pages,
        'header' => '',
        'htmlOptions' => ['class' => 'flex space-x-2 text-sm'],
        'selectedPageCssClass' => 'bg-blue-500 text-white px-2 py-1 rounded',
    ));
    ?>


</div>
