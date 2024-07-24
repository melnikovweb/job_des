import { qsa, addGlobalEventListener } from "../utils/dom"

let controller = null

export async function FileGeneration(e) {
  try {
    if (!e) return

    e.preventDefault()

    if (controller) {
      controller.abort()
    }

    controller = new AbortController()
    const signal = controller.signal

    const formData = new FormData()

    let args = e.target.dataset

    Object.entries(args).map((arg) => {
      let key = arg[0]
      let value = arg[1]

      formData.append(key, value)
    })
    formData.append("action", "generate_files")

    fetch(themeVars.ajax, {
      method: "POST",
      body: formData,
      headers: {
        Accept: "application/json",
      },
      signal: signal,
    })
      .then((response) => {
        if (response.ok) {
          return response.json()
        } else {
          throw new Error("Failed to fetch data")
        }
      })
      .then((data) => {
        const fileName = e.target.dataset.fileName

        data.json && saveJSON(data.json, `${fileName}.json`)
        data.csv && saveCSV(data.csv, `${fileName}.csv`)
        data.pdf && savePDF(data.pdf, `${fileName}.pdf`)
      })
      .catch((error) => {
        console.error("Error: ", error)
      })
  } catch (err) {
    console.error(err)
  }
}

function saveJSON(data, fileName) {
  if (!fileName) fileName = "report.json"

  downloadBlobFile("text/json", fileName, data)
}

function saveCSV(data, fileName) {
  if (!fileName) fileName = "report.csv"

  downloadBlobFile("text/csv", fileName, data)
}

function savePDF(data, fileName) {
  if (!fileName) fileName = data.filename
  let fileURL = data.url
  downloadUrlFile(fileURL, fileName)
}

function downloadBlobFile(blobType = "text/csv", fileName, data) {
  if (!data) {
    console.error("No data")
    return
  }

  if (typeof data === "object") {
    data = JSON.stringify(data, undefined, 4)
  }

  const blob = new Blob([data], { type: blobType })
  const link = document.createElement("a")

  link.download = fileName

  link.href = window.URL.createObjectURL(blob)
  link.dataset.downloadurl = [blobType, link.download, link.href].join(":")

  const evt = new MouseEvent("click", {
    view: window,
    bubbles: true,
    cancelable: true,
  })

  link.dispatchEvent(evt)
  link.remove()
}

function downloadUrlFile(url, fileName) {
  fetch(url, { method: "get", mode: "no-cors", referrerPolicy: "no-referrer" })
    .then((res) => res.blob())
    .then((res) => {
      const aElement = document.createElement("a")
      aElement.setAttribute("download", fileName)
      const href = URL.createObjectURL(res)
      aElement.href = href
      aElement.setAttribute("target", "_blank")
      aElement.click()
      URL.revokeObjectURL(href)
    })
}

export default function initFileGeneration() {
  const tables = qsa('[data-file-action]')
  if (!tables) return

  addGlobalEventListener('click', '[data-file-action]', FileGeneration)
}
