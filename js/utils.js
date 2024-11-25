function focusOnTopmostError() {
	var invalidFields = document.getElementsByClassName('invalid');
	if (invalidFields) {
		invalidFields[0].focus();
	}
	return;
}

function registerRequiredListeners() {
	for (var id in listeners) {
		if (!document.getElementById(id)) {
			continue;
		}
		document.getElementById(id).addEventListener(listeners[id][0], listeners[id][1]);
	}
}

function showErrorMessage(id, message) {
    const element = document.getElementById(id);
    const messageTarget = document.getElementById(`${id}-hint`);

    if (!element || !messageTarget) return;

    removeErrorMessage(id); 

    element.classList.add('invalid');
    if (element.tagName !== 'DIV') {
        element.setAttribute("aria-invalid", "true");
    }

    messageTarget.classList.add("error-message");
    messageTarget.innerHTML = message; 
}

function removeErrorMessage(id) {
    const element = document.getElementById(id);
    const messageTarget = document.getElementById(`${id}-hint`);

    if (!element || !messageTarget) return;

    element.classList.remove('invalid');
    if (element.tagName !== 'DIV') {
        element.setAttribute("aria-invalid", "false");
    }

    messageTarget.classList.remove("error-message");
    messageTarget.innerHTML = '';
}

var isOpen = false;
function toggleMenu() {
	var btn = document.getElementById("dropdown-menu-toggle");
	var links = document.getElementById("dropdown-link-container");
	isOpen = !isOpen;
	btn.setAttribute("data-open", isOpen);
	links.setAttribute("data-open", isOpen);
}


const getPreferredTheme = () => {
  if (typeof localStorage !== 'undefined') {
    const savedTheme = localStorage.getItem('theme');
    if (savedTheme) return savedTheme;
  }

  if (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches) {
    return 'dark';
  }

  return 'light';
};

const applyTheme = (theme) => {
  const rootElement = document.documentElement;

  if (theme === 'dark') {
    rootElement.classList.add('dark');
  } else {
    rootElement.classList.remove('dark');
  }
};

const currentTheme = getPreferredTheme();
applyTheme(currentTheme);

if (typeof localStorage !== 'undefined') {
  localStorage.setItem('theme', currentTheme);
}

const toggleTheme = () => {
  const rootElement = document.documentElement;
  const isDarkMode = rootElement.classList.contains('dark');

  rootElement.classList.toggle('dark');

  localStorage.setItem('theme', isDarkMode ? 'light' : 'dark');
};


