export default function template (data) {

  return `<div class="report-table__body_row">
      <div class="report-table__body_title">
        <p>${data.title}</p>
        <span>${data.subtitle}</span>
        <a href="${data.link}" class="sk-btn sk-btn--primary sk-btn--sm">${themeVars.textGoToReport}
          <svg>
            <use href="#icon-arrow-right"></use>
          </svg>
        </a>
      </div>

      <p class="report-table__body_date">${data.year}</p>

      <ul class="report-table__body_items">
      ${ Object.values(data.reports).map((item, i) => {
          return reportItem(item, i)
        }).join("")
      }
      ${ data.count > 6 ? `<li class="report-table__body_items-btn">
        <button class="sk-btn sk-btn--link sk-btn--sm" data-action="loadmore" data-offset="6">${themeVars.textLoadMore}
          <svg>
            <use href="#icon-plus"></use>
          </svg>
        </button>
      </li>` : ''}
      </ul>
      <a href="${data.link}" class="report-table__body_link sk-btn sk-btn--primary sk-btn--sm">${themeVars.textGoToReport}
          <svg>
            <use href="#icon-arrow-right"></use>
          </svg>
        </a>
    </div>`
}

function reportItem(item, i) {
  let files = [
    {
      url: 'url',
      filename: `${item.signatory}-${item.year}`.replace(/\s+/g, '-'),
      subtype: 'pdf',
      fileAction: 'download',
      fileType: "pdf",
      parentPost: item.id,
    },
    {
      url: 'url',
      filename: `${item.signatory}-${item.year}`.replace(/\s+/g, '-'),
      subtype: 'csv',
      fileAction: 'generate',
      fileType: "csv",
      parentPost: item.id,
    },
    {
      url: 'url',
      filename: `${item.signatory}-${item.year}`.replace(/\s+/g, '-'),
      subtype: 'json',
      fileAction: 'generate',
      fileType: "json",
      parentPost: item.id,
    },
  ]

  return `<li class="report-table__body_items-signatory ${i > 5 ? 'hidden' : ''}">
    <span>${item.signatory}</span>
    ${ Object.values(files).map((file) => {
        return reportFiles(file) // TODO: replace
    }).join("")}
    </li>`
}

function reportFiles(item) {
  return`
  <ol class="files">
      <li class="files__item">
        <button data-file-action="${item.fileAction}" data-file-type="${item.fileType}" data-parent-post="${item.parentPost}" data-file-name="${item.filename}">
          <svg><use href="#icon-download-2"></use></svg>
        </button>
        <span class="format">${item.subtype}</span>
      </li>
  </ol>`
}