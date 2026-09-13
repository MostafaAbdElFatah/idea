import Alpine from 'alpinejs';

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

Alpine.data('passwordField', (label = 'Password', initialValue = '') => ({
    value: initialValue,
    visible: false,

    toggle() {
        this.visible = !this.visible;
    },

    get inputType() {
        return this.visible ? 'text' : 'password';
    },

    get toggleLabel() {
        return `${this.visible ? 'Hide' : 'Show'} ${label.toLowerCase()}`;
    },

    get hasValue() {
        return this.value.length > 0;
    },

    get strength() {
        return getPasswordStrength(this.value);
    },

    get level() {
        return strengthLevels[this.strength - 1];
    },

    segmentClass(index) {
        return index < this.strength ? this.level.color : 'bg-input';
    },
}));
