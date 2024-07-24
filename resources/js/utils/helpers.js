export const getSiblings = n => [...n.parentElement.children].filter(c=>c.nodeType == 1 && c!=n);

export function handleArrayReplace(oldArray, newValue) {
    if (!Array.isArray(oldArray)) oldArray = [`${oldArray}`]

    let result = oldArray

    !oldArray.some(i => i === newValue)
        ? result.push(newValue)
        : result = result.filter(i => i !== newValue)

    if (result.length === 0) return null

    return result.join(',')
}

export const isObjEmpty = (obj) => Object.keys(obj).length === 0 && obj.constructor === Object
