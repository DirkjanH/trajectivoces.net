((Joomla) => {

  if (!Joomla) {
    throw new Error('Joomla API is not properly initiated');
  }
  /**
   * An object holding all the information of the selected file in media manager
   * eg:
   * {
   *   extension: "pdf"
   *   fileType: "application/pdf"
   *   path: "ark-files:/powered_by.pdf"
   * }
   */


  Joomla.selectedMediaFile = {};
  /**0
   * Event Listener that updates the Joomla.selectedMediaFile
   * to the selected file in the media manager
   */

  window.document.addEventListener('onMediaFileSelected', e => {
    Joomla.selectedMediaFile = e.detail;
    const currentModal = Joomla.Modal.getCurrent();
    const container = currentModal.querySelector('.modal-body');
    const optionsEl = container.querySelector('ark-field-mediamore');

    if (optionsEl) {
      optionsEl.parentNode.removeChild(optionsEl);
    } // No extra attributes (lazy, alt) for fields


    if (container.closest('joomla-field-media')) {
      return;
    }
	
    if (Joomla.selectedMediaFile.path) {
      container.insertAdjacentHTML('afterbegin', `
<ark-field-mediamore
  parent-id="${currentModal.id}"
  summary-label="${Joomla.Text._('JFIELD_MEDIA_SUMMARY_LABEL')}"
  title-label="Title"
  text-label="Link Text"
  download-check-label="Download"
  download-label="Download Filename"
  classes-label="Classes"
></ark-field-mediamore>
`);
}
})
  /**
   * Method to check if passed param is HTMLElement
   *
   * @param o {string|HTMLElement}  Element to be checked
   *
   * @returns {boolean}
   */

  const isElement = o => typeof HTMLElement === 'object' ? o instanceof HTMLElement : o && typeof o === 'object' && o.nodeType === 1 && typeof o.nodeName === 'string';
  /**
   * Method to safely append parameters to a URL string
   *
   * @param url   {string}  The URL
   * @param key   {string}  The key of the parameter
   * @param value {string}  The value of the parameter
   *
   * @returns {string}
   */


  const appendParam = (url, key, value) => {
    const newKey = encodeURIComponent(key);
    const newValue = encodeURIComponent(value);
    const r = new RegExp(`(&|\\?)${key}=[^&]*`);
    let s = url;
    const param = `${newKey}=${newValue}`;
    s = s.replace(r, `$1${param}`);

    if (!RegExp.$1 && s.includes('?')) {
      return `${s}&${param}`;
    }

    if (!RegExp.$1 && !s.includes('?')) {
      return `${s}?${param}`;
    }

    return s;
  };
  /**
   * Method to append the image in an editor or a field
   *
   * @param resp
   * @param editor
   * @param fieldClass
   */


  const execTransform = (resp, editor, fieldClass) => {
    if (resp.success === true) {
	  if (resp.data[0].url) {
		if (/ark-/.test(resp.data[0].adapter)) 
		{
		  const {
			rootFull
		  } = Joomla.getOptions('system.paths'); // eslint-disable-next-line prefer-destructuring

		  Joomla.selectedMediaFile.url = resp.data[0].url.split(rootFull)[1];
		} 
	  }else {
        Joomla.selectedMediaFile.url = false;
      }
	  
	  
      if (Joomla.selectedMediaFile.url) {
        let attribs;
        let title = '';
		let docText = '';
		let download = '';
        let classes = '';
        let fileElement = '';

        if (!isElement(editor)) {


		  attribs = fieldClass; 

          if (attribs) {
			  
			title = attribs.getAttribute('doc-title') ? ` title="${attribs.getAttribute('doc-title')}"` : '';  
			  
            if (attribs.getAttribute('download-check') === 'true') {
              download = ' download=""';
            }
			
			if (attribs.getAttribute('doc-text')) {
              docText = attribs.getAttribute('doc-text');
            }
			
			if (attribs.getAttribute('download-value')) {
				download = ` download="${attribs.getAttribute('download-value')}"`;
			}
			
			classes = attribs.getAttribute('doc-classes') ? ` class="${attribs.getAttribute('doc-classes')}"` : '';
				
			
          }

           const linkValue = docText ? docText  : Joomla.selectedMediaFile.url;
	   
           fileElement = `<a href="${Joomla.selectedMediaFile.url}"${title}${classes}${download}/>${linkValue}</a>`;
          
	      if (attribs) {
            attribs.remove();
          }
		  
		  Joomla.editors.instances[editor].replaceSelection(fileElement);
          
          //Joomla.editors.instances[editor].replaceSelection(fileElement);
        } 
      }
    }
  };
  /**
   * Method that resolves the real url for the selected image
   *
   * @param data        {object}         The data for the detail
   * @param editor      {string|object}  The data for the detail
   * @param fieldClass  {HTMLElement}    The fieldClass for the detail
   *
   * @returns {void}
   */


  Joomla.getFile = (data, editor, fieldClass) => new Promise((resolve, reject) => {
    if (!data || typeof data === 'object' && (!data.path || data.path === '')) {
      Joomla.selectedMediaFile = {};
      resolve({
        resp: {
          success: false
        }
      });
      return;
    }

    const apiBaseUrl = `${Joomla.getOptions('system.paths').rootFull}administrator/index.php?option=com_media`;
    Joomla.request({
      url: `${apiBaseUrl}&task=api.files&url=true&path=${data.path}&${Joomla.getOptions('csrf.token')}=1&mediatypes=3`,
      method: 'GET',
      perform: true,
      headers: {
        'Content-Type': 'application/json'
      },
      onSuccess: response => {
        const resp = JSON.parse(response);
        resolve(execTransform(resp, editor, fieldClass));
      },
      onError: err => {
        reject(err);
      }
    });
  });
  /**
   * A sipmle Custom Element for adding alt text and controlling
   * the lazy loading on a selected image
   *
   * Will be rendered only for editor content images
   * Attributes:
   * - parent-id: the id of the parent media field {string}
   * - lazy-label: The text for the checkbox label {string}
   * - alt-label: The text for the alt label {string}
   * - is-lazy: The value for the lazyloading (calculated, defaults to 'true') {string}
   * - alt-value: The value for the alt text (calculated, defaults to '') {string}
   */


const innerHtml = `\`<style>
	ark-field-mediamore details {
	 position:absolute;
	 background:#f5f5f5;
	 border-radius:.25rem;
	}
	ark-field-mediamore label.input-group-text {
	 width:auto;
	}
	ark-field-mediamore summary {
	 padding:1rem;
	 background-color:#e3e3e3;
	 border-radius:.25rem;
	 font-weight:500
	}
	ark-field-mediamore details .form-group {
	 margin:1rem!important
	}
</style>
<details open>
  <summary>\${this.summarytext}</summary>
  <div class="">
	 <div class="form-group">
      <div class="input-group">
        <label class="input-group-text" for="\${this.parentId}-title">\${this.titletext}</label>
        <input class="form-control" type="text" id="\${this.parentId}-title" />
      </div>
    </div>
	<div class="form-group">
      <div class="input-group">
        <label class="input-group-text" for="\${this.parentId}-text">\${this.doctexttext}</label>
        <input class="form-control" type="text" id="\${this.parentId}-text" />
      </div>
    </div>
    <div class="form-group">
      <div class="input-group">
        <label class="input-group-text" for="\${this.parentId}-download">\${this.downloadtext}</label>
        <input class="form-control" type="text" id="\${this.parentId}-download" />
      </div>
    </div>
    <div class="form-group">
      <div class="form-check">
        <input class="form-check-input" type="checkbox" id="\${this.parentId}-download-check">
        <label class="form-check-label" for="\${this.parentId}-download-check">\${this.downloadchecktext}</label>
      </div>
    </div>
    <div class="form-group">
      <div class="input-group">
        <label class="input-group-text" for="\${this.parentId}-classes">\${this.classestext}</label>
        <input class="form-control" type="text" id="\${this.parentId}-classes" />
      </div>
    </div>
  </div>
	  </details>\``

const classDef = `(()=> {
	if(customElements.get('ark-field-mediamore'))
		return;
	
	class ArkFieldMediaMediaMore extends HTMLElement {
	  constructor() {
      super();
	  
	  this.titleInputFn = this.titleInputFn.bind(this)
	  this.textInputFn = this.textInputFn.bind(this)
      this.downloadInputFn = this.downloadInputFn.bind(this);
      this.downloadCheckFn = this.downloadCheckFn.bind(this);
      this.docClassesFn = this.docClassesFn.bind(this);
    }
	

	 get parentId() {
      return this.getAttribute('parent-id');
    }

    get titletext() {
      return this.getAttribute('title-label');
    }
	
	get doctexttext() {
      return this.getAttribute('text-label');
    }

    get downloadtext() {
      return this.getAttribute('download-label');
    }

    get downloadchecktext() {
      return this.getAttribute('download-check-label');
    }

	get classestext() {
      return this.getAttribute('classes-label');
    }


    get summarytext() {
      return this.getAttribute('summary-label');
    }


    connectedCallback() {	
	
	  
		
      this.innerHTML =  ${innerHtml};
	  
	  this.titleInput = this.querySelector(\`#\${this.parentId}-title\`);
      this.titleInput.addEventListener('input', this.titleInputFn);
	  this.textInput = this.querySelector(\`#\${this.parentId}-text\`);
      this.textInput.addEventListener('input', this.textInputFn);
	  this.downloadInput = this.querySelector(\`#\${this.parentId}-download\`);
      this.downloadInput.addEventListener('input', this.downloadInputFn);
	  this.downloadCheck = this.querySelector(\`#\${this.parentId}-download-check\`);
      this.downloadCheck.addEventListener('input', this.downloadCheckFn);
      this.docClasses = this.querySelector(\`#\${this.parentId}-classes\`);
      this.docClasses.addEventListener('input', this.docClassesFn);
	  
	  // Set initial values

	  this.setAttribute('doc-title', '');
	  this.setAttribute('doc-text', '');
      this.setAttribute('download-value', '');
      this.setAttribute('download-check', false);
      this.setAttribute('doc-classes', '');
    

    }

    disconnectedCallback() {

	  this.titleInput.removeEventListener('input', this.titleInputFn);
	  this.textInput.removeEventListener('input', this.textInputFn);
      this.downloadInput.removeEventListener('input', this.downloadInputFn);
      this.downloadCheck.removeEventListener('input', this.downloadCheckFn);
      this.docClasses.removeEventListener('input', this.docClassesFn);
	  
	  this.innerHTML = '';
	  
	  
	  
    }

	titleInputFn(e) {
      this.setAttribute('doc-title', e.target.value);
    }
	
	textInputFn(e) {
      this.setAttribute('doc-text', e.target.value);
    }

    downloadInputFn(e) {
      this.setAttribute('download-value', e.target.value.replace(/"/g, '&quot;'));
    }

    downloadCheckFn(e) {
      this.setAttribute('download-check', !!e.target.checked);
    }

    docClassesFn(e) {
      this.setAttribute('doc-classes', e.target.value);
    }

  }

  
  customElements.define('ark-field-mediamore', ArkFieldMediaMediaMore);
})();  
  `  
	if(!parent.document.getElementById('arkmedmore2'))
	{  
		const script = parent.document.createElement("script");
		script.type  = "text/javascript";
		script.id = "arkmedmore2"
		const inlineScript = document.createTextNode(classDef);
		script.appendChild(inlineScript); 
		parent.document.body.appendChild(script);
	} 
})(Joomla);