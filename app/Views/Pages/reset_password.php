<div class="container py-5">
    <h2>Reset Password</h2>
    <?php if(session()->getFlashdata('error')): ?>
        <div class="alert alert-danger"><?php echo session()->getFlashdata('error'); ?></div>
    <?php endif; ?>
    <form method="post" action="/forgot/update">
        <input type="hidden" name="token" value="<?php echo $token; ?>">
        <div class="mb-3">
            <label for="password" class="form-label">New Password</label>
            <input type="password" class="form-control" id="password" name="password" required>
        </div>
        <button type="submit" class="btn btn-success">Update Password</button>
    </form>
</div>