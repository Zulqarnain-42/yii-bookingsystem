<?php
$userId = Yii::app()->user->id;
$user = Users::model()->findByPk($userId);

if (!$user) {
    throw new CHttpException(404, 'User not found.');
}
?>

<div class="bg-gray-100 flex items-center justify-center px-4 py-10">
  <div class="max-w-xl w-full bg-white p-8 rounded-xl shadow-lg">
    <h2 class="text-2xl font-bold text-gray-800 mb-6">My Profile</h2>

    <!-- User Info Form -->
    <form id="profile-update-form">
      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
          <label class="text-sm text-gray-600 font-medium">Username</label>
          <input type="text" value="<?php echo CHtml::encode($user->username); ?>" disabled class="w-full border bg-gray-100 px-3 py-2 rounded">
        </div>
        <div>
          <label class="text-sm text-gray-600 font-medium">Email</label>
          <input type="text" value="<?php echo CHtml::encode($user->email); ?>" disabled class="w-full border bg-gray-100 px-3 py-2 rounded">
        </div>
        <div>
          <label class="text-sm text-gray-600 font-medium">Full Name</label>
          <input type="text" value="<?php echo CHtml::encode($user->full_name); ?>" disabled class="w-full border bg-gray-100 px-3 py-2 rounded">
        </div>
        <div>
          <label class="text-sm text-gray-600 font-medium">Role</label>
          <input type="text" value="<?php echo CHtml::encode($user->role); ?>" disabled class="w-full border bg-gray-100 px-3 py-2 rounded">
        </div>
        <div>
          <label class="text-sm text-gray-600 font-medium">Mobile Number</label>
          <input type="text" name="phone" id="phone" value="<?php echo CHtml::encode($user->phone); ?>" class="w-full border px-3 py-2 rounded focus:outline-none focus:ring focus:border-indigo-300">
        </div>
      </div>

      <!-- Save Button -->
      <div class="mt-6 text-right">
        <button type="submit" class="bg-indigo-600 text-white px-6 py-2 rounded hover:bg-indigo-700 transition">
          Save Changes
        </button>
      </div>
    </form>

    <!-- Feedback -->
    <div id="feedback" class="mt-4 text-sm"></div>
  </div>
</div>

<script>
document.getElementById('profile-update-form').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const phone = document.getElementById('phone').value;

    fetch('<?php echo Yii::app()->createUrl("user/updateProfile"); ?>', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify({ phone: phone })
    })
    .then(res => res.json())
    .then(data => {
        const feedback = document.getElementById('feedback');
        if (data.success) {
            feedback.innerHTML = '<p class="text-green-600">Profile updated successfully.</p>';
        } else {
            feedback.innerHTML = '<p class="text-red-600">Error updating profile.</p>';
        }
    })
    .catch(err => {
        document.getElementById('feedback').innerHTML = '<p class="text-red-600">Server error.</p>';
    });
});
</script>
