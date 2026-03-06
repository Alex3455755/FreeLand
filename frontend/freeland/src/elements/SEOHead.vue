<!-- src/components/SEOHead.vue -->
<template>
  <div style="display: none;">
    <!-- Этот компонент не рендерит видимый контент, только обновляет head -->
  </div>
</template>

<script>
export default {
  name: 'SEOHead',
  props: {
    title: {
      type: String,
      default: ''
    },
    description: {
      type: String,
      default: ''
    },
    keywords: {
      type: String,
      default: ''
    },
    ogTitle: {
      type: String,
      default: ''
    },
    ogDescription: {
      type: String,
      default: ''
    },
    ogImage: {
      type: String,
      default: ''
    },
    canonical: {
      type: String,
      default: ''
    },
    noindex: {
      type: Boolean,
      default: false
    }
  },
  watch: {
    title: {
      handler: 'updateTitle',
      immediate: true
    },
    description: {
      handler: 'updateMetaDescription',
      immediate: true
    },
    keywords: {
      handler: 'updateMetaKeywords',
      immediate: true
    },
    ogTitle: {
      handler: 'updateOpenGraph',
      immediate: true
    },
    ogDescription: {
      handler: 'updateOpenGraph',
      immediate: true
    },
    ogImage: {
      handler: 'updateOpenGraph',
      immediate: true
    },
    canonical: {
      handler: 'updateCanonical',
      immediate: true
    },
    noindex: {
      handler: 'updateRobots',
      immediate: true
    }
  },
  mounted() {
    this.updateAllMeta();
  },
  methods: {
    updateAllMeta() {
      this.updateTitle();
      this.updateMetaDescription();
      this.updateMetaKeywords();
      this.updateOpenGraph();
      this.updateCanonical();
      this.updateRobots();
    },
    
    updateTitle() {
      if (this.title) {
        document.title = this.title;
      }
    },
    
    updateMetaDescription() {
      if (this.description) {
        let meta = document.querySelector('meta[name="description"]');
        if (meta) {
          meta.setAttribute('content', this.description);
        } else {
          meta = document.createElement('meta');
          meta.name = 'description';
          meta.content = this.description;
          document.head.appendChild(meta);
        }
      }
    },
    
    updateMetaKeywords() {
      if (this.keywords) {
        let meta = document.querySelector('meta[name="keywords"]');
        if (meta) {
          meta.setAttribute('content', this.keywords);
        } else {
          meta = document.createElement('meta');
          meta.name = 'keywords';
          meta.content = this.keywords;
          document.head.appendChild(meta);
        }
      }
    },
    
    updateOpenGraph() {
      // Обновляем Open Graph теги
      const ogProperties = [
        'og:title',
        'og:description',
        'og:image',
        'og:url',
        'og:type',
        'twitter:card',
        'twitter:title',
        'twitter:description',
        'twitter:image'
      ];
      
      // Удаляем старые OG теги
      ogProperties.forEach(prop => {
        const oldMeta = document.querySelector(`meta[property="${prop}"]`);
        if (oldMeta) {
          oldMeta.remove();
        }
      });
      
      // Создаем новые OG теги
      if (this.ogTitle || this.title) {
        const meta = document.createElement('meta');
        meta.setAttribute('property', 'og:title');
        meta.content = this.ogTitle || this.title;
        document.head.appendChild(meta);
      }
      
      if (this.ogDescription || this.description) {
        const meta = document.createElement('meta');
        meta.setAttribute('property', 'og:description');
        meta.content = this.ogDescription || this.description;
        document.head.appendChild(meta);
      }
      
      if (this.ogImage) {
        const meta = document.createElement('meta');
        meta.setAttribute('property', 'og:image');
        meta.content = this.ogImage;
        document.head.appendChild(meta);
      }
      
      if (this.canonical) {
        const meta = document.createElement('meta');
        meta.setAttribute('property', 'og:url');
        meta.content = this.canonical;
        document.head.appendChild(meta);
      }
      
      // Open Graph type
      const typeMeta = document.createElement('meta');
      typeMeta.setAttribute('property', 'og:type');
      typeMeta.content = 'article';
      document.head.appendChild(typeMeta);
      
      // Twitter Card
      const twitterCard = document.createElement('meta');
      twitterCard.setAttribute('property', 'twitter:card');
      twitterCard.content = 'summary_large_image';
      document.head.appendChild(twitterCard);
      
      if (this.ogTitle || this.title) {
        const twitterTitle = document.createElement('meta');
        twitterTitle.setAttribute('property', 'twitter:title');
        twitterTitle.content = this.ogTitle || this.title;
        document.head.appendChild(twitterTitle);
      }
      
      if (this.ogDescription || this.description) {
        const twitterDesc = document.createElement('meta');
        twitterDesc.setAttribute('property', 'twitter:description');
        twitterDesc.content = this.ogDescription || this.description;
        document.head.appendChild(twitterDesc);
      }
      
      if (this.ogImage) {
        const twitterImage = document.createElement('meta');
        twitterImage.setAttribute('property', 'twitter:image');
        twitterImage.content = this.ogImage;
        document.head.appendChild(twitterImage);
      }
    },
    
    updateCanonical() {
      if (this.canonical) {
        // Удаляем старый canonical
        const oldCanonical = document.querySelector('link[rel="canonical"]');
        if (oldCanonical) {
          oldCanonical.remove();
        }
        
        // Создаем новый
        const link = document.createElement('link');
        link.rel = 'canonical';
        link.href = this.canonical;
        document.head.appendChild(link);
      }
    },
    
    updateRobots() {
      let meta = document.querySelector('meta[name="robots"]');
      const content = this.noindex ? 'noindex, nofollow' : 'index, follow';
      
      if (meta) {
        meta.setAttribute('content', content);
      } else {
        meta = document.createElement('meta');
        meta.name = 'robots';
        meta.content = content;
        document.head.appendChild(meta);
      }
    }
  }
}
</script>