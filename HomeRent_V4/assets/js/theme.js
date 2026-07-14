/* File: /assets/js/theme.js */
(()=>{const k='theme';const b=document.body;const t=localStorage.getItem(k)||'light';b.dataset.theme=t;window.toggleTheme=()=>{const n=b.dataset.theme==='dark'?'light':'dark';b.dataset.theme=n;localStorage.setItem(k,n);};})();
