const debounce = (callback, delay = 300) => {
  let timer;
  return (...args) => {
    clearTimeout(timer);
    timer = setTimeout(() => callback(...args), delay);
  };
};

function makeElement(tag, className, text) {
  const element = document.createElement(tag);
  if (className) element.className = className;
  if (text !== undefined && text !== null) element.textContent = String(text);
  return element;
}

function makeQuestionCard(question) {
  const article = makeElement('article', 'surface-card question-card d-flex gap-3');
  const votes = makeElement('div', 'vote-count', question.votes);
  votes.append(makeElement('small', '', 'голосов'));

  const content = makeElement('div');
  const title = makeElement('a', 'question-title', question.title);
  title.href = `/questions/${encodeURIComponent(question.id)}`;
  content.append(title, makeElement('p', 'question-description', question.excerpt));

  const tags = makeElement('div', 'd-flex flex-wrap gap-2 mb-2');
  for (const tag of question.tags) tags.append(makeElement('span', 'tag', tag));
  content.append(tags);

  const meta = makeElement('div', 'question-meta');
  meta.append(
    makeElement('span', '', `${question.answer_count} ответа`),
    makeElement('span', '', `${question.views} просмотров`),
    makeElement('span', '', `${question.author} · ${question.created_label}`),
  );
  content.append(meta);
  article.append(votes, content);
  return article;
}

function initQuestionSearch() {
  const form = document.querySelector('.question-search');
  const input = form?.querySelector('[name="q"]');
  const category = form?.querySelector('[name="category"]');
  const list = document.getElementById('question-list');
  const status = document.getElementById('question-search-status');
  if (!form || !input || !list || !status) return;

  let controller;
  const search = async () => {
    controller?.abort();
    const requestController = new AbortController();
    controller = requestController;
    list.setAttribute('aria-busy', 'true');
    status.textContent = 'Ищем вопросы…';

    const params = new URLSearchParams({ q: input.value.trim() });
    if (category?.value) params.set('category', category.value);

    try {
      const response = await fetch(`/api/questions/?${params.toString()}`, {
        headers: { Accept: 'application/json' },
        signal: requestController.signal,
      });
      if (!response.ok) throw new Error(`HTTP ${response.status}`);
      const questions = await response.json();
      if (!Array.isArray(questions)) throw new TypeError('Expected a list of questions');

      if (questions.length === 0) {
        const empty = makeElement('article', 'surface-card p-4 text-center text-secondary', 'Вопросы не найдены. Измените запрос и попробуйте ещё раз.');
        list.replaceChildren(empty);
        status.textContent = 'По вашему запросу ничего не найдено.';
      } else {
        list.replaceChildren(...questions.map(makeQuestionCard));
        status.textContent = `Найдено вопросов: ${questions.length}`;
      }
    } catch (error) {
      if (error.name === 'AbortError') return;
      console.error('Не удалось выполнить поиск вопросов:', error);
      status.textContent = 'Поиск временно недоступен. Проверьте соединение и повторите попытку.';
    } finally {
      if (!requestController.signal.aborted && controller === requestController) list.removeAttribute('aria-busy');
    }
  };

  const debouncedSearch = debounce(search, 300);
  input.addEventListener('input', debouncedSearch);
  category?.addEventListener('change', search);
  form.addEventListener('submit', (event) => {
    event.preventDefault();
    search();
  });
}

const questionFieldMessages = {
  title: {
    valueMissing: 'Введите заголовок вопроса.',
    tooShort: 'Заголовок должен содержать не менее 15 символов.',
  },
  body: {
    valueMissing: 'Опишите вопрос подробнее.',
    tooShort: 'Описание должно содержать не менее 30 символов.',
  },
  category_id: { valueMissing: 'Выберите категорию.' },
};

function setQuestionCustomValidity(form) {
  const title = form.elements.title;
  const body = form.elements.body;
  title.setCustomValidity('');
  body.setCustomValidity('');
  if (title.value.trim() && title.value.trim().length < 15) {
    title.setCustomValidity('Заголовок должен содержать не менее 15 символов.');
  }
  if (body.value.trim() && body.value.trim().length < 30) {
    body.setCustomValidity('Описание должно содержать не менее 30 символов.');
  }
}

function questionFieldMessage(field) {
  if (field.validity.valid) return '';
  if (field.validity.customError) return field.validationMessage;
  const messages = questionFieldMessages[field.name] ?? {};
  if (field.validity.valueMissing) return messages.valueMissing ?? 'Заполните это поле.';
  if (field.validity.tooShort) return messages.tooShort ?? `Введите не менее ${field.minLength} символов.`;
  return 'Проверьте значение этого поля.';
}

function showQuestionField(field) {
  const message = questionFieldMessage(field);
  const feedback = document.getElementById(`${field.id}-error`);
  field.classList.toggle('is-invalid', Boolean(message));
  if (message) field.setAttribute('aria-invalid', 'true');
  else field.removeAttribute('aria-invalid');
  if (feedback) feedback.textContent = message;
  return !message;
}

function initQuestionForm() {
  const form = document.getElementById('question-form');
  if (!form) return;
  form.noValidate = true;
  const fields = [...form.querySelectorAll('input[name="title"], textarea[name="body"], select[name="category_id"]')];
  const refresh = () => {
    setQuestionCustomValidity(form);
    return fields.map(showQuestionField);
  };

  form.addEventListener('focusout', (event) => {
    if (!fields.includes(event.target)) return;
    setQuestionCustomValidity(form);
    showQuestionField(event.target);
  });
  form.addEventListener('input', (event) => {
    if (fields.includes(event.target) && event.target.classList.contains('is-invalid')) {
      setQuestionCustomValidity(form);
      showQuestionField(event.target);
    }
  });
  form.addEventListener('change', (event) => {
    if (fields.includes(event.target) && event.target.classList.contains('is-invalid')) {
      setQuestionCustomValidity(form);
      showQuestionField(event.target);
    }
  });
  form.addEventListener('reset', () => {
    requestAnimationFrame(() => {
      fields.forEach((field) => {
        field.setCustomValidity('');
        field.classList.remove('is-invalid');
        field.removeAttribute('aria-invalid');
        const feedback = document.getElementById(`${field.id}-error`);
        if (feedback) feedback.textContent = '';
      });
    });
  });
  form.addEventListener('submit', (event) => {
    const valid = refresh();
    const firstInvalid = fields[valid.indexOf(false)];
    if (firstInvalid) {
      event.preventDefault();
      firstInvalid.focus();
    }
  });
}

function initDeleteConfirmation() {
  const modalElement = document.getElementById('confirm-delete-modal');
  const form = document.getElementById('confirm-delete-form');
  const name = document.getElementById('confirm-delete-name');
  if (!modalElement || !form || !name || !window.bootstrap) return;

  const modal = window.bootstrap.Modal.getOrCreateInstance(modalElement);
  const submit = form.querySelector('[type="submit"]');
  let trigger;
  document.addEventListener('click', (event) => {
    const button = event.target instanceof Element ? event.target.closest('[data-delete-url]') : null;
    if (!button) return;
    const target = new URL(button.dataset.deleteUrl, window.location.origin);
    if (target.origin !== window.location.origin || !/^\/questions\/\d+$/.test(target.pathname)) return;
    trigger = button;
    form.action = target.pathname;
    name.textContent = button.dataset.questionTitle ?? '';
    submit.disabled = false;
    modal.show();
  });
  modalElement.addEventListener('hidden.bs.modal', () => {
    if (trigger?.isConnected) trigger.focus();
  });
  form.addEventListener('submit', () => { submit.disabled = true; });
}

initQuestionSearch();
initQuestionForm();
initDeleteConfirmation();
