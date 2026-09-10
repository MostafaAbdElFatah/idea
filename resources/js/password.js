const strengthLevels = [
    {
        label: 'Very weak',
        color: 'bg-error',
        text: 'text-error',
        hint: 'Add more characters to make it harder to guess.',
    },
    {
        label: 'Weak',
        color: 'bg-warning',
        text: 'text-warning',
        hint: 'Try adding uppercase letters, numbers, or symbols.',
    },
    {
        label: 'Medium',
        color: 'bg-info',
        text: 'text-info',
        hint: 'A few more characters can make this much stronger.',
    },
    {
        label: 'Strong',
        color: 'bg-success',
        text: 'text-success',
        hint: 'Nice choice. This password is difficult to guess.',
    },
];

const getPasswordStrength = (password) => {
let score = 0;

if (password.length >= 8) {
    score++;
}

if (password.length >= 12) {
    score++;
}

if (/[a-z]/.test(password) && /[A-Z]/.test(password)) {
    score++;
}

if (/\d/.test(password) || /[^A-Za-z0-9]/.test(password)) {
    score++;
}

return Math.max(1, Math.min(score, 4));

};

const initializePasswordToggle = () => {
document
.querySelectorAll('[data-password-toggle]')
.forEach((toggle) => {
toggle.addEventListener('click', () => {
const input = document.getElementById(
toggle.dataset.passwordToggle
);

            if (!input) {
                return;
            }

            const isVisible = input.type === 'text';

            input.type = isVisible ? 'password' : 'text';

            toggle.setAttribute(
                'aria-pressed',
                String(!isVisible)
            );

            toggle.setAttribute(
                'aria-label',
                `${isVisible ? 'Show' : 'Hide'} password`
            );

            toggle
                .querySelector(
                    '[data-password-eye="hidden"]'
                )
                ?.classList.toggle(
                    'hidden',
                    !isVisible
                );

            toggle
                .querySelector(
                    '[data-password-eye="shown"]'
                )
                ?.classList.toggle(
                    'hidden',
                    isVisible
                );
        });
    });

};

const initializePasswordStrength = () => {
document
.querySelectorAll('[data-password-strength]')
.forEach((meter) => {
const input = document.getElementById(
meter.dataset.passwordStrength
);

        const label = meter.querySelector(
            '[data-strength-label]'
        );

        const hint = meter.querySelector(
            '[data-strength-hint]'
        );

        const segments = meter.querySelectorAll(
            '[data-strength-segment]'
        );

        if (!input) {
            return;
        }

        const updateStrength = () => {
            const password = input.value;

            if (!password) {
                meter.classList.add('hidden');
                return;
            }

            const strength = getPasswordStrength(password);
            const level = strengthLevels[strength - 1];

            meter.classList.remove('hidden');

            label.textContent = level.label;
            hint.textContent = level.hint;

            label.className =
                `font-semibold ${level.text}`;

            segments.forEach((segment, index) => {
                segment.className =
                    `h-1.5 flex-1 rounded-full transition-colors duration-300 ${
                        index < strength
                            ? level.color
                            : 'bg-input'
                    }`;
            });
        };

        input.addEventListener('input', updateStrength);

        if (input.value) {
            updateStrength();
        }
    });

};

const initializePasswordComponents = () => {
initializePasswordToggle();
initializePasswordStrength();
};

if (document.readyState === 'loading') {
document.addEventListener(
'DOMContentLoaded',
initializePasswordComponents
);
} else {
initializePasswordComponents();
}
