# 將新的資訊添加在'112-2數學系課程資訊.xlsx'後，並輸出檔案
import os
import pandas as pd

current_directory = os.path.dirname(os.path.abspath(__file__))
excel_file_path = os.path.join(
    current_directory, '111(2)數學系書卷獎科目學期成績第一名學生(請授課教師指定)(彙整)(R).xlsx')  # 讀入欲合併資料的檔案
target = pd.read_excel(excel_file_path, header=0,
                       sheet_name=0)  # 欲合併的資料需在第一個sheet中


excel_file_path = os.path.join(current_directory, '112-2數學系課程資訊.xlsx')
org = pd.read_excel(excel_file_path, header=1, sheet_name=None)
df_1 = org['D1B-數學系資訊數學組']
df_2 = org['D31-數學系應用數學組']

# 透過比對系級與課程名稱(欲合併資料所在的資料表需包含此兩項資訊)，將資料合併在正確的位置
# 將df2(dataframe)中的資料附在df1(dataframe)最後，grade(string)為系級的欄位名稱，course(string)為課程名稱的欄位名稱，info_to_add(string)為欲合併資料的欄位名稱。後三個參數皆為df2的欄位名稱。


def merge(df1, df2, grade, course, info_to_merge):
    df2 = df2[[grade, course, info_to_merge]]  # 系級、課程名稱、欲合併資訊
    df1[info_to_merge] = None
    grade_1 = df1.columns.get_loc("開課單位")
    grade_t = df2.columns.get_loc(grade)
    course_1 = df1.columns.get_loc("課程名稱")
    course_t = df2.columns.get_loc(course)
    target_1 = df1.columns.get_loc('姓名')
    target_t = df2.columns.get_loc(info_to_merge)

    for i in range(df2.shape[0]):
        for j in range(df1.shape[0]):
            if (df2.iloc[i, grade_t] == df1.iloc[j, grade_1]) and (df2.iloc[i, course_t] == df1.iloc[j, course_1]):
                df1.iloc[j, target_1] = df2.iloc[i, target_t]


merge(df_1, target, "111學年度下學期", "課程名稱", "姓名")
merge(df_2, target, "111學年度下學期", "課程名稱", "姓名")
excel_file_path = os.path.join(current_directory, '112-2數學系課程資訊(合併完成).xlsx')
with pd.ExcelWriter(excel_file_path) as writer:
    df_1.to_excel(writer, sheet_name='Sheet1', index=False)
    df_2.to_excel(writer, sheet_name='Sheet2', index=False)


# target = target[['111學年度下學期', '課程名稱', '姓名']]  # 系級、課程名稱、欲添加資訊
# df1['姓名'] = None
# df2['姓名'] = None
# grade_1 = df1.columns.get_loc("開課單位")
# grade_t = target.columns.get_loc('111學年度下學期')
# course_1 = df1.columns.get_loc("課程名稱")
# course_t = target.columns.get_loc('課程名稱')
# target_1 = df1.columns.get_loc('姓名')
# target_t = target.columns.get_loc('姓名')

# for i in range(target.shape[0]):
#    for j in range(df1.shape[0]):
#        if (target.iloc[i, grade_t] == df1.iloc[j, grade_1]) and (target.iloc[i, course_t] == df1.iloc[j, course_1]):
#            df1.iloc[j, target_1] = target.iloc[i, target_t]
