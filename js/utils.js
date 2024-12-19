let isOpen = false;
let lastScrollPosition = 10;

const focusOnTopmostError = () => {
    const invalidFields = document.getElementsByClassName('invalid');
    if (invalidFields.length > 0) invalidFields[0].focus();
};

const showErrorMessage = (id, message) => {
    const element = document.getElementById(id);
    const messageTarget = document.getElementById(`${id}-hint`);
    if (!element || !messageTarget) return;
    removeErrorMessage(id);
    element.classList.add('invalid');
    if (element.tagName !== 'DIV') element.setAttribute('aria-invalid', 'true');
    messageTarget.classList.add('error-message');
    messageTarget.innerHTML= message;
};

const removeErrorMessage = (id) => {
    const element = document.getElementById(id);
    const messageTarget = document.getElementById(`${id}-hint`);
    if (!element || !messageTarget) return;
    element.classList.remove('invalid');
    if (element.tagName !== 'DIV') element.setAttribute('aria-invalid', 'false');
    messageTarget.classList.remove('error-message');
    messageTarget.innerHTML = '';
};

const toggleMenu = () => {
    const btn = document.getElementById('dropdown-menu-toggle');
    const links = document.getElementById('dropdown-link-container');
    isOpen = !isOpen;
    btn.setAttribute('data-open', isOpen);
    links.setAttribute('data-open', isOpen);
};

// Gestione tema
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
    rootElement.classList.toggle('dark', theme === 'dark');
};

const toggleTheme = () => {
    const rootElement = document.documentElement;
    const isDarkMode = rootElement.classList.contains('dark');
    rootElement.classList.toggle('dark');
    localStorage.setItem('theme', isDarkMode ? 'light' : 'dark');
};

const currentTheme = getPreferredTheme();
applyTheme(currentTheme);
if (typeof localStorage !== 'undefined') {
    localStorage.setItem('theme', currentTheme);
}

const updateFilterVisibility = () => {
    const filter = document.getElementById('filter');
    if (!filter) return;

    const type = filter.value;
    const inputDt = document.getElementById('dt_filter');
    const labelDt = document.getElementById('dt_filter_l');
    const inputAllgs = document.getElementById('allg_filter');
    const labelAllgs = document.getElementById('allg_filter_l');
    const inputOrder = document.getElementById('order_filter'); 
    const labelOrder = document.getElementById('order_filter_l');

    if (inputDt && labelDt) {
        inputDt.hidden = true;
        labelDt.hidden = true;
    };

    if (inputAllgs && labelAllgs) {
        inputAllgs.hidden = true;
        labelAllgs.hidden = true;
        inputAllgs.value = '';
    };

    if (inputOrder && labelOrder) {
        inputOrder.hidden = true;
        labelOrder.hidden = true;
        inputOrder.value = '';
    }

    if (type && type === 'dish_type') {
        inputDt.hidden = false;
        labelDt.hidden = false;
    } else if (type && type === 'allgs') {
        inputAllgs.hidden = false;
        labelAllgs.hidden = false; 
    } else if (type && type === 'cal' || type && type === 'prt' || type && type === 'carbo' || type && type === 'fat') { 
        inputOrder.hidden = false;
        labelOrder.hidden = false; 
    }

};



function isScrollingDownward() {
    let isDownward = false;
	  let currentScrollPosition = window.pageYOffset;
	  if (currentScrollPosition > lastScrollPosition) { isDownward = true };
	  lastScrollPosition = currentScrollPosition;
	  return isDownward;
}

function handlePageScroll() {
	  let scrollToTopButton = document.getElementById("back-to-top");
	  if (scrollToTopButton != null) {
		    let viewportHeight = window.innerHeight;
		    let currentScrollPosition = window.pageYOffset;

		    if (isScrollingDownward() || currentScrollPosition == 0) {
			      scrollToTopButton.classList.remove('show');
		    } else if (isScrollingDownward() === false && currentScrollPosition > viewportHeight/5) {
			      scrollToTopButton.classList.add('show');
		    }
	  }
}

document.addEventListener('DOMContentLoaded', () => {
    if (document.body.classList.contains('search-page')) {
        updateFilterVisibility();
        const filter = document.getElementById('filter');
        if (filter) {
            filter.addEventListener('change', updateFilterVisibility);
        }
    }
});


const registerRequiredListeners = () => {
    for (const id in listeners) {
        const element = document.getElementById(id);
        if (element) element.addEventListener(listeners[id][0], listeners[id][1]);
    }
};

window.addEventListener("scroll", handlePageScroll);
