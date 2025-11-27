<div class="container py-5">
    <h2>Forgot Password</h2>
    <?php if(session()->getFlashdata('error')): ?>
        <div class="alert alert-danger"><?php echo session()->getFlashdata('error'); ?></div>
    <?php endif; ?>
    <?php if(session()->getFlashdata('success')): ?>
        <div class="alert alert-success"><?php echo session()->getFlashdata('success'); ?></div>
    <?php endif; ?>
    <form method="post" action="/forgot/send">
        <div class="mb-3">
            <label for="email" class="form-label">Email address</label>
            <input type="email" class="form-control" id="email" name="email" required>
        </div>
        <button type="submit" class="btn btn-primary">Send Reset Link</button>
    </form>
</div>