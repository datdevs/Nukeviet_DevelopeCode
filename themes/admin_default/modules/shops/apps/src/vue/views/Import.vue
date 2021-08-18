<template>
  <div v-loading="loading" :class="$style.ImportPage">
    <el-page-header title="Quay lại" content="Import File Excel" @back="goBackConponPage"></el-page-header>
    <div :class="$style.InstructionWrap">
      <el-alert :class="$style.Mb10" title="Mẫu file Excel import mã giảm giá:" type="info" show-icon :closable="false">
        <a :class="$style.FileLink" :href="excelFile"><excel-icon />Coupon-import.xlsx</a>
      </el-alert>
      <el-alert title="Hướng dẫn:" type="info" show-icon>
        <p><strong>Tiêu đề, Mã giảm giá, Giá trị giảm:</strong> Bắt buộc nhập, không được để trống</p>
        <p><strong>Giá trị giảm:</strong>
          <ul>
            <li>Nếu giảm tiền mặt: nhập giá trị số bất kỳ (ví dụ: 10000, 20000, 30000,...)</li>
            <li>Nếu giảm theo phần trăm: nhập giá trị phần trăm (ví dụ: 10%, 20%, 30%,...)</li>
          </ul>
        </p>
        <p><strong>Giá trị đơn hàng tối thiểu:</strong> Không bắt buộc, yêu cầu nhập số (ví dụ: mua tối thiểu 1 triệu mới áp dụng giảm giá nhập 1000000)</p>
        <p><strong>Ngày bắt đầu:</strong> Nếu bắt đầu ngày hiện tại nhập số 0 hoặc để trống</p>
        <p><strong>Ngày kết thúc:</strong> Không bắt buộc</p>
        <p><strong>Ngày bắt đầu, Ngày kết thúc:</strong> Yêu cầu nhập theo định dạng ngày tháng dd/mm/yyyy (ví dụ: 01/01/2021)</p>
        <p><strong>Số lượng:</strong> Không bắt buộc, yêu cầu nhập số (ví dụ: 0, 1, 2, 3,...)</p>
        <p><strong>Sản phẩm áp dụng:</strong> Không bắt buộc, yêu cầu nhập chính xác tên sản phẩm và cách nhau bởi dấu phẩy (ví dụ: Sách A1, Sách A2)</p>
      </el-alert>
    </div>
    <div :class="$style.UploadWrap">
      <UploadExcelFile :on-success="handleSuccess" :before-upload="resetData" />
    </div>
    <div v-if="data.length > 0" :class="$style.TableWrap">
      <el-alert
        :class="$style.Mb10"
        title="Vui lòng kiểm tra mã giảm giá và bắt đầu nhập dữ liệu."
        type="warning"
        show-icon
        :closable="false"
      ></el-alert>
      <el-alert
        v-for="(e, i) in error"
        :key="`error_${i}`"
        :class="$style.Mb10"
        :title="e"
        type="error"
        show-icon
        :closable="false"
      ></el-alert>
      <div :class="$style.Mb30">
        <el-button
          type="primary"
          size="small"
          icon="el-icon-download"
          :disabled="error.length > 0"
          :loading="stateSave"
          @click="handleSaveData"
          >Nhập dữ liệu</el-button
        >
      </div>
      <el-table :data="data" border fit show-overflow-tooltip :max-height="528">
        <el-table-column
          v-for="field in fields"
          :key="`coupon_${field.key}`"
          :min-width="field.mwidth"
          :label="field.label"
          :fixed="field.key === 'title'"
          :prop="field.prop"
        >
          <template slot-scope="{ row }">
            {{ rowRender(row, field) }}
          </template>
        </el-table-column>
      </el-table>
    </div>
    <div v-if="data.length === 0 && success" :class="$style.SuccessNoti">
      <el-alert
        title="Danh sách mã giảm giá đã được thêm vào thành công, bạn có thể click Quay lại để kiểm tra danh sách mã giảm giá vừa thêm vào hoặc tiếp tục chọn file để thêm danh sách mã giảm giá mới."
        type="success"
        show-icon
        :closable="false"
      ></el-alert>
    </div>
  </div>
</template>

<script>
import UploadExcelFile from '@/vue/components/UploadExcelFile'
import ExcelIcon from '@/vue/components/ExcelIcon'
import mapKeys from 'lodash.mapkeys'
import mapValues from 'lodash.mapvalues'
import { toThousandFilter, uppercaseFirst } from '../../filters'

const { jQuery, script_name, nv_name_variable, nv_module_name, nv_fc_variable } = window

export default {
  filters: {
    toThousandFilter,
    uppercaseFirst,
  },
  components: {
    UploadExcelFile,
    ExcelIcon,
  },
  data() {
    return {
      fields: [
        { key: 'title', label: 'Tiêu đề', prop: 'title', required: true, unique: true, mwidth: '200' },
        { key: 'code', label: 'Mã giảm giá', prop: 'code', required: true, mwidth: '120' },
        { key: 'discount', label: 'Giá trị giảm', prop: 'discount', required: true, mwidth: '120' },
        { key: 'total_amount', label: 'Giá trị đơn hàng tối thiểu', prop: 'total_amount', mwidth: '150' },
        { key: 'date_start', label: 'Ngày bắt đầu', prop: 'date_start', mwidth: '120' },
        { key: 'date_end', label: 'Ngày kết thúc', prop: 'date_end', mwidth: '120' },
        { key: 'uses_per_coupon', label: 'Số lượng', prop: 'uses_per_coupon', mwidth: '90' },
        { key: 'product', label: 'Sản phẩm áp dụng', prop: 'product', mwidth: '50' },
      ],
      data: [],
      headers: [],
      loading: false,
      success: false,
      stateSave: false,
      tableNewKey: {
        'Tiêu đề': 'title',
        'Mã giảm giá': 'code',
        'Giá trị giảm': 'discount',
        'Giá trị đơn hàng tối thiểu': 'total_amount',
        'Ngày bắt đầu': 'date_start',
        'Ngày kết thúc': 'date_end',
        'Số lượng': 'uses_per_coupon',
        'Sản phẩm áp dụng': 'product',
      },
      error: [],
      excelFile: '/themes/admin_default/modules/shops/coupon-import.xlsx',
    }
  },
  methods: {
    goBackConponPage() {
      window.location.href =
        script_name + '?' + nv_name_variable + '=' + nv_module_name + '&' + nv_fc_variable + '=coupons'
    },
    isValidDate(d) {
      let date_regex = /(((0|1)[0-9]|2[0-9]|3[0-1])\/(0[1-9]|1[0-2])\/((19|20)\d\d))$/
      return date_regex.test(d)
    },
    resetData() {
      this.loading = true
      this.data = []
      this.headers = []
      this.error = []
      this.success = false
      return true
    },
    handleSuccess({ header, results }) {
      const comp = this
      // let checkDiscount = false
      let newResult = results.map((r, i) => {
        // checkDiscount = true
        let newR = mapKeys(r, (value, key) => {
          return comp.tableNewKey[key]
        })
        if (newR.title && newR.title !== '') {
          newR.title = uppercaseFirst(String(newR.title))
        } else {
          comp.error.push(
            `Lỗi: Tiêu đề mã giảm giá không được để trống, vui lòng kiểm tra lại file Excel dòng số ${i + 2}.`
          )
        }
        if (newR.code && newR.code !== '') {
          newR.code = String(newR.code).toUpperCase()
        } else {
          comp.error.push(
            `Lỗi: Code mã giảm giá không được để trống, vui lòng kiểm tra lại file Excel dòng số ${i + 2}.`
          )
        }
        if (!newR.hasOwnProperty('total_amount')) newR.total_amount = 0
        if (!newR.hasOwnProperty('uses_per_coupon')) newR.uses_per_coupon = 0
        if (!newR.hasOwnProperty('discount') || newR.discount === 0) {
          // checkDiscount = false
          // newR.discount = 0
          comp.error.push(
            `Lỗi: Giá trị mã giảm giá không được để trống hoặc bằng 0, vui lòng kiểm tra lại file Excel dòng số ${
              i + 2
            }.`
          )
        }
        newR.discount = String(newR.discount)
        const regexPercent = new RegExp('%')
        if (regexPercent.test(newR.discount)) {
          newR.type = 'p'
        }else if (isNaN(Number(newR.discount))) {
          newR.type = 'g'
        }else {
          newR.type = 'f'
        }
        if (
          !newR.hasOwnProperty('date_start') ||
          newR.date_start === 0 ||
          newR.date_start === '0' ||
          newR.date_start === ''
        ) {
          const ofdt = { year: 'numeric', month: '2-digit', day: '2-digit' }
          newR.date_start = new Date().toLocaleDateString('vi-VN', ofdt)
        } else if (!comp.isValidDate(newR.date_start)) {
          comp.error.push(
            `Lỗi: Ngày bắt đầu không hợp lệ (định dạng cho phép dd/mm/yyy), vui lòng kiểm tra lại file Excel dòng số ${
              i + 2
            }.`
          )
        }
        if (newR.hasOwnProperty('date_end')) {
          if (newR.date_end === 0 || newR.date_end === '0' || newR.date_end === '') {
            newR.date_end = ''
          }else if (!comp.isValidDate(newR.date_end)) {
            comp.error.push(
              `Lỗi: Ngày kết thúc không hợp lệ (định dạng cho phép dd/mm/yyy), vui lòng kiểm tra lại file Excel dòng số ${
                i + 2
              }.`
            )
          }
        }
        if (newR.hasOwnProperty('product')) {
          newR.product = newR.product.trim()
          newR.product = newR.product.replaceAll(', ', ',')
        }
        newR = mapValues(newR, (value, key) => {
          let newValue = String(value)
          if (newR.type === 'p' && ['discount'].indexOf(key) > -1) {
            newValue = parseFloat(newValue.replace(/[^0-9.]/g, ''))
          }
          // if (['discount', 'total_amount'].indexOf(key) > -1) {
          //   newValue = parseFloat(newValue.replace(/[^0-9.]/g, ''))
          // }
          if (['total_amount'].indexOf(key) > -1) {
            newValue = parseFloat(newValue.replace(/[^0-9.]/g, ''))
          }
          if (['uses_per_coupon'].indexOf(key) > -1) {
            newValue = parseInt(newValue.replace(/[^0-9.]/g, ''))
          }
          return newValue
        })
        // if ((newR.discount === 0 || isNaN(newR.discount)) && checkDiscount) {
        //   comp.error.push(
        //     `Lỗi: Giá trị mã giảm giá không được để trống hoặc bằng 0, vui lòng kiểm tra lại file Excel dòng số ${
        //       i + 2
        //     }.`
        //   )
        // }
        if (isNaN(newR.total_amount)) {
          comp.error.push(
            `Lỗi: Đơn hàng tối thiểu bắt buộc là số, vui lòng kiểm tra lại file Excel dòng số ${
              i + 2
            }.`
          )
        }
        if (isNaN(newR.uses_per_coupon)) {
          comp.error.push(
            `Lỗi: Số lượng bắt buộc là số, vui lòng kiểm tra lại file Excel dòng số ${
              i + 2
            }.`
          )
        }
        return newR
      })
      this.headers = header
      this.data = newResult
      this.loading = false
    },
    handleSaveData() {
      const comp = this
      comp.stateSave = true
      const jsonData = JSON.stringify(this.data)
      jQuery
        .post(
          script_name +
            '?' +
            nv_name_variable +
            '=' +
            nv_module_name +
            '&' +
            nv_fc_variable +
            '=coupons_import&import=1',
          { data: jsonData }
        )
        .done(() => {
          comp.stateSave = false
          comp.data = []
          comp.headers = []
          comp.error = []
          comp.success = true
          comp.$message({
            message: 'Danh sách mã giảm giá đã được thêm vào thành công.',
            duration: 6000,
            type: 'success',
          })
        })
        .fail(() => {
          comp.stateSave = false
          comp.$message({
            message: 'Có lỗi xảy ra ở server, mã giảm giá vẫn chưa được nhập.',
            duration: 6000,
            type: 'error',
          })
        })
    },
    rowRender(row, field) {
      if (field.key === 'discount' && row['type'] === 'p') {
        return row[field.prop] + '%'
      } else if (
        (field.key === 'discount' && row['type'] === 'f') ||
        ['total_amount', 'uses_per_coupon'].indexOf(field.key) > -1
      ) {
        return toThousandFilter(row[field.prop])
      } else {
        return row[field.prop]
      }
    },
  },
}
</script>

<style lang="scss" module>
.ImportPage {
  min-height: 500px;
}

.InstructionWrap,
.UploadWrap,
.TableWrap,
.SuccessNoti {
  margin-top: 30px;
}

.FlexCenter {
  align-items: center;
  display: flex;
}

.Mb10 {
  margin-bottom: 10px;
}

.Mb30 {
  margin-bottom: 30px;
}

.FileLink {
  color: #1D6F42;
  font-size: 18px;
  display: inline-flex;
  align-items: center;

  svg {
    width: 20px;
    margin-right: 5px;
    margin-top: -2px;
  }
}
</style>
