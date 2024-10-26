import os
import pandas as pd

current_directory = os.path.dirname(os.path.abspath(__file__))
excel_file_path = os.path.join(
    current_directory, '111(2)數學系書卷獎科目學期成績第一名學生(請授課教師指定)(彙整)(R).xlsx')
target = pd.read_excel(excel_file_path, header=1,
                       sheet_name=0)  # 只合併一個sheet的資料，
target.columns = target.columns.astype(str)

print(target)

excel_file_path = os.path.join(current_directory, '112-2數學系課程資訊.xlsx')
org = pd.read_excel(excel_file_path, header=1, sheet_name=None)


df1 = org['D1B-數學系資訊數學組']
df2 = org['D31-數學系應用數學組']

target = target[['111學年度下學期', '課程名稱', '姓名']]  # 系級、課程名稱、欲添加資訊
df1['姓名'] = None
df2['姓名'] = None

print(df1.columns)
