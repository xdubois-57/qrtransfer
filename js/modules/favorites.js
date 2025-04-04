import translations from './translations.js';
import formOperations from './form-operations.js';

const FAVORITES_KEY = 'qr_transfer_favorites';

/**
 * Loads favorites from storage and populates the select
 * @param {HTMLSelectElement} [favoritesSelect] - Optional select element, will query if not provided
 */
function loadFavorites(favoritesSelect) {
    favoritesSelect = favoritesSelect || document.getElementById('favorites');
    if (!favoritesSelect) {
        console.error('Favorites select not found');
        return;
    }

    const favorites = JSON.parse(localStorage.getItem(FAVORITES_KEY) || '[]');

    // Clear existing options except the first one (placeholder)
    while (favoritesSelect.options.length > 1) {
        favoritesSelect.remove(1);
    }

    // Add favorites to select
    favorites.forEach((favorite, index) => {
        const option = document.createElement('option');
        option.value = index;
        option.textContent = favorite.beneficiary_name + ' - ' + favorite.beneficiary_iban;
        favoritesSelect.appendChild(option);
    });
}

/**
 * Loads a selected favorite into the form
 */
function loadFavorite() {
    console.log('loadFavorite called');
    const favoritesSelect = document.getElementById('favorites');
    const deleteButton = document.getElementById('delete-favorite');
    const saveButton = document.getElementById('save-favorite');
    const nameInput = document.getElementById('beneficiary_name');
    const ibanInput = document.getElementById('beneficiary_iban');

    if (!favoritesSelect || !deleteButton || !saveButton || !nameInput || !ibanInput) {
        console.error('Missing required elements for loading favorite');
        return;
    }

    const selectedIndex = favoritesSelect.value;
    const favorites = JSON.parse(localStorage.getItem(FAVORITES_KEY) || '[]');

    if (selectedIndex === '') {
        // No favorite selected, enable inputs
        nameInput.disabled = false;
        ibanInput.disabled = false;
        deleteButton.disabled = true;
        saveButton.textContent = translations.translate('save_favorite');
        return;
    }

    const favorite = favorites[selectedIndex];
    if (!favorite) {
        console.error('Selected favorite not found:', selectedIndex);
        return;
    }

    // Load favorite data
    nameInput.value = favorite.beneficiary_name;
    ibanInput.value = favorite.beneficiary_iban;

    // Disable inputs when a favorite is selected
    nameInput.disabled = true;
    ibanInput.disabled = true;
    deleteButton.disabled = false;
    saveButton.textContent = translations.translate('update_favorite');

    // Trigger change events on the inputs
    nameInput.dispatchEvent(new Event('change', { bubbles: true }));
    ibanInput.dispatchEvent(new Event('change', { bubbles: true }));
}

/**
 * Saves the current form values as a favorite
 */
function saveFavorite() {
    const favoritesSelect = document.getElementById('favorites');
    const saveButton = document.getElementById('save-favorite');
    const nameInput = document.getElementById('beneficiary_name');
    const ibanInput = document.getElementById('beneficiary_iban');

    if (!favoritesSelect || !saveButton || !nameInput || !ibanInput) {
        console.error('Missing required elements for saving favorite');
        return;
    }

    const name = nameInput.value.trim();
    const iban = ibanInput.value.trim();

    if (!name || !iban) {
        alert(translations.translate('fill_required_fields'));
        return;
    }

    let favorites = JSON.parse(localStorage.getItem(FAVORITES_KEY) || '[]');
    const selectedIndex = favoritesSelect.value;

    // Check for duplicates
    const existingIndex = favorites.findIndex(f => 
        f.beneficiary_name === name && f.beneficiary_iban === iban
    );

    if (existingIndex !== -1 && existingIndex !== parseInt(selectedIndex)) {
        alert(translations.translate('favorite_exists'));
        return;
    }

    const favorite = {
        beneficiary_name: name,
        beneficiary_iban: iban
    };

    if (selectedIndex !== '') {
        // Update existing favorite
        favorites[selectedIndex] = favorite;
    } else {
        // Add new favorite
        favorites.push(favorite);
    }

    localStorage.setItem(FAVORITES_KEY, JSON.stringify(favorites));
    loadFavorites(favoritesSelect);

    // Select the saved favorite
    const newIndex = selectedIndex !== '' ? selectedIndex : (favorites.length - 1).toString();
    favoritesSelect.value = newIndex;

    // Update UI state
    nameInput.disabled = true;
    ibanInput.disabled = true;
    document.getElementById('delete-favorite').disabled = false;
    saveButton.textContent = translations.translate('update_favorite');
}

/**
 * Deletes the currently selected favorite
 */
function deleteFavorite() {
    const favoritesSelect = document.getElementById('favorites');
    const deleteButton = document.getElementById('delete-favorite');
    const saveButton = document.getElementById('save-favorite');
    const nameInput = document.getElementById('beneficiary_name');
    const ibanInput = document.getElementById('beneficiary_iban');

    if (!favoritesSelect || !deleteButton || !saveButton || !nameInput || !ibanInput) {
        console.error('Missing required elements for deleting favorite');
        return;
    }

    const selectedIndex = favoritesSelect.value;
    if (selectedIndex === '') return;

    const favorites = JSON.parse(localStorage.getItem(FAVORITES_KEY) || '[]');
    favorites.splice(selectedIndex, 1);
    localStorage.setItem(FAVORITES_KEY, JSON.stringify(favorites));

    // Clear form and enable inputs
    nameInput.value = '';
    ibanInput.value = '';
    nameInput.disabled = false;
    ibanInput.disabled = false;

    // Trigger change events
    nameInput.dispatchEvent(new Event('change', { bubbles: true }));
    ibanInput.dispatchEvent(new Event('change', { bubbles: true }));

    // Update UI
    loadFavorites();
    deleteButton.disabled = true;
    saveButton.textContent = translations.translate('save_favorite');
}

/**
 * Initializes favorites functionality
 */
function initializeFavorites() {
    const favoritesSelect = document.getElementById('favorites');
    if (!favoritesSelect) return;

    // Load initial favorites
    loadFavorites(favoritesSelect);

    // Add change listener
    favoritesSelect.addEventListener('change', loadFavorite);

    // Make functions globally available
    window.saveFavorite = saveFavorite;
    window.deleteFavorite = deleteFavorite;
}

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', initializeFavorites);

export default {
    loadFavorites,
    loadFavorite,
    saveFavorite,
    deleteFavorite,
    initializeFavorites
};
