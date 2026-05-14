declare module '*.vue' {
    import type { DefineComponent } from 'vue'
    const component: DefineComponent<{}, {}, any>
    export default component
}

declare module '@mapbox/mapbox-gl-draw' {
    import { IControl } from 'mapbox-gl'
    interface MapboxDrawOptions {
        displayControlsDefault?: boolean
        controls?: Record<string, any>
        defaultMode?: string
        styles?: any[]
    }
    class MapboxDraw implements IControl {
        constructor(options?: MapboxDrawOptions)
        onAdd(map: any): HTMLElement
        onRemove(map: any): void
        add(geojson: any): string[]
        get(featureId: string): any
        getAll(): any
        set(featureCollection: any): void
        delete(featureId: string): void
        deleteAll(): void
        getSelectedIds(): string[]
        changeMode(mode: string, options?: any): void
        getMode(): string
    }
    export default MapboxDraw
}

interface ImportMetaEnv {
    readonly VITE_MAPBOX_TOKEN: string
}

interface ImportMeta {
    readonly env: ImportMetaEnv
}
