import './stimulus_bootstrap.js';
/*
 * Welcome to your app's main JavaScript file!
 *
 * This file will be included onto the page via the importmap() Twig function,
 * which should already be in your base.html.twig.
 */
import './styles/app.css';
import Swal from 'sweetalert2';

console.log('This log comes from assets/app.js - welcome to AssetMapper! 🎉');

document.addEventListener('submit', e => {
    const form = e.target;

    if (!form.classList.contains('js-delete')) return;

    e.preventDefault(); // Prevent default form submission

    Swal.fire({
      title: "Êtes-vous sûr?",
      text: "Cette action est irréversible!",
      icon: "warning",
      showCancelButton: true,
      confirmButtonColor: "#3085d6",
      cancelButtonColor: "#d33",
      confirmButtonText: "Oui, supprimer!",
      cancelButtonText: "Annuler"
    }).then((result) => {
      if (result.isConfirmed) {
        form.submit(); // Actually submit the form
      }
    });

})

