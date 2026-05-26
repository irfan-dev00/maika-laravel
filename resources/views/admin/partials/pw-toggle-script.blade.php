<script>
(function () {
    // Toggle show/hide password
    document.querySelectorAll('[data-toggle-pw]').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var input = document.getElementById(this.dataset.togglePw);
            var icon = this.querySelector('i');
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.replace('fa-eye', 'fa-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.replace('fa-eye-slash', 'fa-eye');
            }
        });
    });

    // Password strength indicator (untuk field #password)
    var pwInput = document.getElementById('password');
    if (!pwInput) return;

    var wrap  = document.getElementById('strength-wrap');
    var bar   = document.getElementById('strength-bar');
    var label = document.getElementById('strength-label');
    if (!wrap || !bar || !label) return;

    var levels = [
        { max: 20,  cls: 'bg-danger',  text: 'Sangat lemah' },
        { max: 40,  cls: 'bg-warning', text: 'Lemah' },
        { max: 60,  cls: 'bg-info',    text: 'Cukup' },
        { max: 80,  cls: 'bg-primary', text: 'Kuat' },
        { max: 100, cls: 'bg-success', text: 'Sangat kuat' },
    ];

    function calcStrength(pw) {
        var score = 0;
        if (pw.length >= 8)  score += 20;
        if (pw.length >= 12) score += 10;
        if (/[A-Z]/.test(pw)) score += 20;
        if (/[0-9]/.test(pw)) score += 20;
        if (/[^A-Za-z0-9]/.test(pw)) score += 30;
        return Math.min(score, 100);
    }

    pwInput.addEventListener('input', function () {
        var val = this.value;
        if (!val) { wrap.classList.add('d-none'); return; }
        wrap.classList.remove('d-none');
        var score = calcStrength(val);
        var lvl = levels.find(function (l) { return score <= l.max; }) || levels[levels.length - 1];
        bar.style.width = score + '%';
        bar.className = 'progress-bar ' + lvl.cls;
        label.textContent = lvl.text;
    });
})();
</script>
