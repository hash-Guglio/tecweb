const listeners = {
    'username': ['input', validateUserUsername],
    'password' : ["blur", validateUserPassword],
    'password_confirm' : ["blur", validateUserPasswordConfirm],
};

function getFieldValue(fieldName) {
    return document.forms['signupForm'][fieldName]?.value || '';
}

function initFormValidation() {
    const form = document.getElementById("signupForm");
    if (!form) return;

    registerRequiredListeners();
    form.addEventListener("submit", handleFormSubmit);
}

function handleFormSubmit(event) {
    const validations = [
        validateUserUsername(),
        validateUserPassword(),
        validateUserPasswordConfirm()
    ];

    if (!validations.every(Boolean)) {
        focusOnTopmostError();
        event.preventDefault();
    }
}

function validateUserUsername() {
    const id = 'username';
    const username = getFieldValue('username');
    const allowedChars = /^[A-Za-z0-9]+$/;

    if (!allowedChars.test(username)) {
        showErrorMessage(id, '<span lang="en">Username</span> non valido, usa solo lettere o numeri.');
        return false;
    }
    removeErrorMessage(id);
    return true;
}


function validateUserPassword() {
    const id = 'password';
    const password = getFieldValue('password');

    if (password.length < 8) {
        showErrorMessage(id, 'La <span lang="en">password</span> deve contenere almeno 8 caratteri.');
        return false;
    }

    if (!/\d/.test(password) || !/[a-zA-Z]/.test(password)) {
        showErrorMessage(id, 'La <span lang="en">password</span> deve includere almeno una lettera e un numero.');
        return false;
    }

    removeErrorMessage(id);
    return true;
}

function validateUserPasswordConfirm() {
    const id = 'password_confirm';
    const password = getFieldValue('password');
    const password_confirm = getFieldValue('password_confirm');

    if (password === '') {
        removeErrorMessage(id);
        return true;
    }

    if (password !== password_confirm) {
        showErrorMessage(id, 'Le <span lang="en">password</span> non corrispondono.');
        return false;
    }

    removeErrorMessage(id);
    return true;
}

window.addEventListener('load', initFormValidation);
