<table class="table-auto w-full border-collapse border border-gray-300">
    <thead>
        <tr class="bg-gray-100">
            <th class="border px-4 py-2">ID</th>
            <th class="border px-4 py-2">Username</th>
            <th class="border px-4 py-2">Email</th>
            <th class="border px-4 py-2">Role</th>
            <th class="border px-4 py-2">Full Name</th>
            <th class="border px-4 py-2">Phone</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($users as $user): ?>
            <tr data-id="<?php echo $user->id; ?>">
                <td class="border px-4 py-2"><?php echo $user->id; ?></td>
                <td class="border px-4 py-2 service-name"><?php echo CHtml::encode($user->username); ?></td>
                <td class="border px-4 py-2 service-description"><?php echo CHtml::encode($user->email); ?></td>
                <td class="border px-4 py-2 service-description"><?php echo CHtml::encode($user->role); ?></td>
                <td class="border px-4 py-2 service-description"><?php echo CHtml::encode($user->full_name); ?></td>
                <td class="border px-4 py-2 service-description"><?php echo CHtml::encode($user->phone); ?></td>
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
