<div class="container py-5">
    <h2>Email Confirmation</h2>
    <p>Your password reset request was received. Please click the button below to confirm your email and continue resetting your password.</p>
    <form method="get" action="/forgot/reset/<?php echo $token; ?>">
        <button type="submit" class="btn btn-success">Confirm Email & Continue</button>
    </form>
</div>