<?php
/* @var $services Services[] */
/* @var $pages CPagination */
?>

<div class="max-w-7xl mx-auto px-6 sm:px-12 lg:px-20 py-12">

  <h1 class="text-3xl font-extrabold text-center text-gray-900 mb-16">
    Our Premium Services
  </h1>

  <div class="grid gap-10 sm:grid-cols-1 md:grid-cols-2 lg:grid-cols-3 mb-12">
    <?php foreach ($services as $service): ?>
      <div class="bg-white rounded-xl shadow-md p-8 flex flex-col justify-between transform hover:scale-105 transition-transform duration-300">
        <div>
          <h2 class="text-3xl font-semibold text-indigo-700 mb-4"><?php echo CHtml::encode($service->name); ?></h2>
          <p class="text-gray-600 mb-6 leading-relaxed">
            <?php
              $desc = strip_tags($service->description);
              echo CHtml::encode(strlen($desc) > 140 ? substr($desc, 0, 137) . '...' : $desc);
            ?>
          </p>
        </div>
        <a href="<?php echo $this->createUrl('services/view', ['id' => $service->id]); ?>"
          class="mt-auto block text-center bg-indigo-600 text-white font-semibold py-3 rounded-lg hover:bg-indigo-700 focus:outline-none focus:ring-4 focus:ring-indigo-300 shadow-lg transition duration-200">
          View Details
        </a>

      </div>
    <?php endforeach; ?>
  </div>

  <!-- Pagination Links -->
  <div class="flex justify-center space-x-2">
    <?php
    $this->widget('CLinkPager', [
        'pages' => $pages,
        'header' => '',
        'htmlOptions' => ['class' => 'inline-flex space-x-2 text-gray-700'],
        'selectedPageCssClass' => 'bg-indigo-600 text-white rounded px-3 py-1',
        'hiddenPageCssClass' => 'text-gray-400 cursor-not-allowed',
        'maxButtonCount' => 7,
        'nextPageLabel' => 'Next',
        'prevPageLabel' => 'Prev',
    ]);
    ?>
  </div>

</div>
