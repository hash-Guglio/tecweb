const listeners = {
    'username': ['input', validateUserUsername],
    'email': ['input', validateUserEmail],    
    'first_name': ['input', validateUserFirstName], 
    'birth_date': ['input', validateUserBirthDate],
    'new_password' : ["blur", validateUserPassword],
    'new_password_confirm' : ["blur", validateUserPasswordConfirm ],
};

function getFieldValue(fieldName) {
    return document.forms['manage_user'][fieldName]?.value || '';
}

function initFormValidation() {
    const form = document.getElementById("manage_user");
    if (!form) return;

    registerRequiredListeners();
    form.addEventListener("submit", handleFormSubmit);
}

function handleFormSubmit(event) {
    const validations = [
        validateUserUsername(),
        validateUserFirstName(),
        validateUserEmail(),
        validateUserBirthDate(),
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

function validateUserEmail() {
    const id = 'email';
    const email = getFieldValue('email');

    if (!email) {
        removeErrorMessage(id);
        return true;
    }

    if (!isValidEmail(email)) {
        showErrorMessage(id, 'Non un indirizzo <span lang="en">email</span> valido.');
        return false;
    }
    removeErrorMessage(id);
    return true;
}

function isValidEmail(email) {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailRegex.test(String(email).toLowerCase());
}

function validateUserFirstName() {
    const id = 'first_name';
    const first_name = getFieldValue('first_name');
    const allowedChars = /^[A-Za-z\s']*$/;

    if (first_name == null || first_name == '') {
		    removeErrorMessage(id);
		    return true;
	  }

    if (!allowedChars.test(first_name)) {
        showErrorMessage(id, 'Nome non valido, usa solo lettere, spazi o apostrofi.');
        return false;
    }
    removeErrorMessage(id);
    return true;
}

function validateUserPassword() {
    const id = 'new_password';
    const password = getFieldValue('new_password');

    if (password === '') {
        document.forms['manage_user']['old_password'].required = false;
        document.forms['manage_user']['new_password_confirm'].required = false;
        removeErrorMessage(id);
        return true; 
    }

    document.forms['manage_user']['old_password'].required = true;
    document.forms['manage_user']['new_password_confirm'].required = true;

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
    const id = 'new_password_confirm';
    const password = getFieldValue('new_password');
    const password_confirm = getFieldValue('new_password_confirm');

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

function validateUserBirthDate() {}
window.addEventListener('load', initFormValidation);
