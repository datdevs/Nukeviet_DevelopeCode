<template>
  <div :class="$style.UploadComponent">
    <input
      ref="excel-upload-input"
      :class="$style.UploadInput"
      type="file"
      :accept="accept"
      @change="handleFileChange"
    />
    <slot name="body" :show="handleUpload">
      <div
        ref="droppable-area"
        :class="$style.DropArea"
        @click="handleUpload"
        @drop="handleDrop"
        @dragover="handleDragover"
        @dragenter="handleDragover"
        @dragleave="handleDragleave"
      >
        <span>Kéo thả tệp Excel hoặc Nhấn vào đây để chọn file</span>
      </div>
    </slot>
  </div>
</template>

<script>
import XLSX from 'xlsx'

export default {
  name: 'UploadExcelFile',
  props: {
    beforeUpload: Function, // eslint-disable-line
    onSuccess: Function, // eslint-disable-line
    accept: { type: String, default: '.xlsx, .xls, .csv' },
  },
  data() {
    return {
      loading: false,
      excelData: {
        header: null,
        results: null,
      },
    }
  },
  methods: {
    eventLock(e) {
      e.stopPropagation()
      e.preventDefault()
    },
    generateData({ header, results }) {
      this.excelData.header = header
      this.excelData.results = results
      this.onSuccess && this.onSuccess(this.excelData)
      this.$message({
        message: 'Tập tin đã được tải lên thành công.',
        duration: 6000,
        type: 'success',
      })
    },
    handleDrop(e) {
      this.eventLock(e)
      this.removeDropOverClass()
      if (this.loading) return
      const files = e.dataTransfer.files
      if (files.length !== 1) {
        this.$message({
          message: 'Vui lòng tải lên 1 tập tin duy nhất.',
          duration: 6000,
          type: 'error',
        })
        return
      }
      const rawFile = files[0] // only use files[0]

      if (!this.isExcel(rawFile)) {
        this.$message({
          message: 'Vui lòng sử dụng tập tin có phần mở rộng được cho phép (.xlsx, .xls, .csv).',
          duration: 6000,
          type: 'error',
        })
        return false
      }
      this.upload(rawFile)
    },
    addDropOverClass() {
      this.$refs['droppable-area'].classList.add(this.$style.DropOver)
    },
    removeDropOverClass() {
      this.$refs['droppable-area'].classList.remove(this.$style.DropOver)
    },
    handleDragover(e) {
      this.eventLock(e)
      this.addDropOverClass()
      e.dataTransfer.dropEffect = 'copy'
    },
    handleDragleave() {
      this.removeDropOverClass()
    },
    handleUpload() {
      this.$refs['excel-upload-input'].click()
    },
    handleFileChange(e) {
      const files = e.target.files
      const rawFile = files[0] // only use files[0]
      if (!rawFile) return
      this.upload(rawFile)
    },
    upload(rawFile) {
      this.$refs['excel-upload-input'].value = null // fix can't select the same excel

      const validateBeforeUpload = this.beforeUpload ? this.beforeUpload(rawFile) : true
      if (validateBeforeUpload) {
        this.readerData(rawFile)
      }
    },
    readerData(rawFile) {
      this.loading = true
      return new Promise((resolve, reject) => {
        const reader = new FileReader()
        reader.onload = (e) => {
          const data = e.target.result
          const workbook = XLSX.read(data, { type: 'array' })
          const firstSheetName = workbook.SheetNames[0]
          const worksheet = workbook.Sheets[firstSheetName]
          const header = this.getHeaderRow(worksheet)
          const results = XLSX.utils.sheet_to_json(worksheet)
          this.generateData({ header, results })
          this.loading = false
          resolve()
        }
        reader.readAsArrayBuffer(rawFile)
        reader.onerror = reject
      })
    },
    getHeaderRow(sheet) {
      const headers = []
      const range = XLSX.utils.decode_range(sheet['!ref'])
      let C
      const R = range.s.r
      /* start in the first row */
      for (C = range.s.c; C <= range.e.c; ++C) {
        /* walk every column in the range */
        const cell = sheet[XLSX.utils.encode_cell({ c: C, r: R })]
        /* find the cell in the first row */
        let hdr = 'UNKNOWN ' + C // <-- replace with your desired default
        if (cell && cell.t) hdr = XLSX.utils.format_cell(cell)
        headers.push(hdr)
      }
      return headers
    },
    isExcel(file) {
      return /\.(xlsx|xls|csv)$/.test(file.name)
    },
  },
}
</script>

<style lang="scss" module>
.UploadComponent {
  text-align: center;
}

input.UploadInput {
  display: none;
  z-index: -9999;
}

.DropArea {
  border: 1px dashed #8c939d;
  border-radius: 5px;
  color: #8c939d;
  cursor: pointer;
  display: inline-block;
  font-size: 16px;
  height: 120px;
  line-height: 120px;
  padding: 0 50px;
  position: relative;
  text-align: center;
  transition: all 0.3s;
}

.DropOver {
  border-color: #409eff;
}
</style>
