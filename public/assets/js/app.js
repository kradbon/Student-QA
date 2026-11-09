document.addEventListener('DOMContentLoaded', () => {
  const title = document.querySelector('[name="title"]');
  if (!title) return;
  const box = document.createElement('div');
  box.className = 'similar-box mt-2';
  title.parentNode.appendChild(box);
  let timer;
  title.addEventListener('input', () => {
    clearTimeout(timer);
    const q = title.value.trim();
    if (q.length < 4) { box.innerHTML = ''; return; }
    timer = setTimeout(async () => {
      try {
        const response = await fetch(`api/similar-questions.php?q=${encodeURIComponent(q)}`);
        const result = await response.json();
        box.innerHTML = result.data.length
          ? '<div class="small text-secondary mb-1">Similar questions</div>' + result.data.map(item =>
              `<a class="d-block small py-1" href="?page=question&id=${Number(item.id)}">${escapeHtml(item.title)}</a>`).join('')
          : '<span class="small text-secondary">No similar questions found.</span>';
      } catch { box.textContent = 'Could not load suggestions.'; }
    }, 250);
  });
});
function escapeHtml(value) {
  return String(value).replace(/[&<>"']/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#039;'}[c]));
}
