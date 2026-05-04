/**
 * Детерминированный «github-like» идентикон (симметричная сетка 5×5) + URL аватара пользователя.
 */

function hashSeed(str) {
  let h = 5381
  const s = String(str || '')
  for (let i = 0; i < s.length; i++) {
    h = ((h << 5) + h) ^ s.charCodeAt(i)
  }
  return Math.abs(h) >>> 0
}

export function identiconDataUrl(seedStr, size = 128) {
  const hash = hashSeed(seedStr || 'anonymous')
  const grid = 5
  const half = Math.ceil(grid / 2)
  let bits = hash
  const hue = hash % 360
  const sat = 50 + ((hash >> 3) % 28)
  const light = 38 + ((hash >> 7) % 18)
  const fill = `hsl(${hue} ${sat}% ${light}%)`
  const bg = `hsl(${hue} ${Math.min(sat + 12, 92)}% 96%)`

  const cell = size / grid
  const rects = []

  for (let y = 0; y < grid; y++) {
    for (let x = 0; x < half; x++) {
      const on = bits & 1
      bits >>>= 1
      if (!on) continue
      const xMirror = grid - 1 - x
      rects.push(`<rect x="${x * cell}" y="${y * cell}" width="${cell}" height="${cell}" fill="${fill}"/>`)
      if (xMirror !== x) {
        rects.push(`<rect x="${xMirror * cell}" y="${y * cell}" width="${cell}" height="${cell}" fill="${fill}"/>`)
      }
    }
  }

  const svg = `<svg xmlns="http://www.w3.org/2000/svg" width="${size}" height="${size}" viewBox="0 0 ${size} ${size}"><rect width="100%" height="100%" fill="${bg}"/>${rects.join('')}</svg>`
  return `data:image/svg+xml;utf8,${encodeURIComponent(svg)}`
}

/**
 * @param {object|null} user — объект с полями id, login, full_name, name, avatar
 * @param {string} [apiBaseUrl] — если avatar относительный (/storage/...), дополняем origin API
 */
export function avatarSrc(user, apiBaseUrl = '') {
  if (!user) return identiconDataUrl('0')

  const raw = user.avatar != null && String(user.avatar).trim()
  if (raw) {
    if (raw.startsWith('http://') || raw.startsWith('https://') || raw.startsWith('data:')) {
      return raw
    }
    if (raw.startsWith('/')) {
      const base = String(apiBaseUrl || '').replace(/\/$/, '')
      if (base) return `${base}${raw}`
      if (typeof window !== 'undefined') return `${window.location.origin}${raw}`
      return raw
    }
    return raw
  }

  const seed = [user.id, user.login, user.full_name, user.name].filter(Boolean).join('|')
  return identiconDataUrl(seed || String(user.id ?? 'user'))
}
