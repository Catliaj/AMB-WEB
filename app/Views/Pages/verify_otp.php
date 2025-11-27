<div class="container py-5">
    <h2>Enter OTP Code</h2>
    <?php if(session()->getFlashdata('error')): ?>
        <div class="alert alert-danger"><?php echo session()->getFlashdata('error'); ?></div>
    <?php endif; ?>
    <form method="post" action="/forgot/verify-otp">
        <input type="hidden" name="token" value="<?php echo $token; ?>">
        <div class="mb-3">
            <label for="otp_code" class="form-label">OTP Code</label>
            <input type="text" class="form-control" id="otp_code" name="otp_code" required>
        </div>
        <button type="submit" class="btn btn-success">Verify OTP</button>
    </form>
</div>